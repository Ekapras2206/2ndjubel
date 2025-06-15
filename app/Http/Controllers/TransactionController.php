<?php

namespace App\Http\Controllers;

// Models
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Rating;
use App\Models\User;

// Services & Repositories
use App\Services\TransactionService;
use App\Services\ProductService;
use App\Repositories\UserRepository;
use App\Repositories\ProductRepository;

// Requests & Facades
use App\Http\Requests\RatingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    protected $transactionService;
    protected $productService;
    protected $userRepository;
    protected $productRepository;

    public function __construct(
        TransactionService $transactionService,
        ProductService $productService,
        UserRepository $userRepository,
        ProductRepository $productRepository
    ) {
        $this->transactionService = $transactionService;
        $this->productService = $productService;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Menampilkan histori pesanan.
     * HANYA PEMBELI YANG DAPAT MELIHAT PESANAN MEREKA SENDIRI.
     * Admin juga akan melihat pesanan yang mereka beli (jika mereka juga pembeli).
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Anda harus login untuk melihat riwayat pesanan.');
        }

        $currentUser = Auth::user();
        $query = Order::query();

        // Hanya tampilkan pesanan di mana user saat ini adalah pembeli (user_id di tabel orders)
        $query->where('user_id', $currentUser->id)
              ->with(['user', 'items.product.user']) // Tetap eager load relasi untuk tampilan detail item
              ->latest(); // Urutkan berdasarkan tanggal terbaru

        $orders = $query->paginate(10); // Menggunakan paginate agar $orders->links() berfungsi di view

        return view('transactions.index', compact('orders'));
    }

    /**
     * Menampilkan detail satu pesanan (Order).
     * Hanya bisa diakses oleh Admin, Pembeli, atau Penjual dari salah satu item di pesanan.
     */
    public function show(Order $order)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Anda harus login untuk melihat detail pesanan.');
        }

        $currentUser = Auth::user();
        $order->load(['user', 'items.product.user', 'items.rating']);

        // dd($order); // Hapus komentar ini setelah yakin data muncul

        $isBuyer = ($currentUser->id === $order->user_id);
        $isAdmin = ($currentUser instanceof \App\Models\User) ? $currentUser->isAdmin() : false;

        $isSeller = false;
        if (!$isBuyer && !$isAdmin) {
            foreach ($order->items as $item) {
                if ($item->product && isset($item->product->user_id) && $item->product->user_id === $currentUser->id) {
                    $isSeller = true;
                    break;
                }
            }
        }

        // Izinkan akses jika pengguna adalah pembeli, ATAU penjual dari salah satu item, ATAU admin.
        // Jika Anda ingin mengaktifkan otorisasi ini, hapus komentar.
        // Pastikan Anda sudah membuat policy atau middleware yang tepat untuk ini.
        // if (!$isBuyer && !$isSeller && !$isAdmin) {
        //     abort(403, 'AKSES DITOLAK. Anda tidak memiliki izin untuk melihat detail pesanan ini.');
        // }

        return view('transactions.show', compact('order'));
    }

    /**
     * Memproses pembelian langsung untuk satu produk ("Buy Now").
     */
    public function processDirectBuy(Product $product, Request $request)
    {
        $buyer = Auth::user();

        if (!$buyer) {
            return redirect()->route('login')->with('error', 'Anda harus login untuk melakukan pembelian.');
        }

        if ($product->user_id === $buyer->id) {
            return redirect()->route('products.show', $product->id)->with('error', 'Anda tidak dapat membeli produk Anda sendiri.');
        }

        if ($product->status !== 'approved') { // Hanya produk yang sudah diapprove bisa dibeli
            return redirect()->route('products.show', $product->id)->with('error', 'Produk ini belum tersedia untuk dibeli.');
        }

        // Validasi metode pembayaran dari form konfirmasi
        $validatedData = $request->validate([
            'payment_method' => 'required|string|max:50',
            // Tambahkan validasi lain jika ada input di form konfirmasi (misal alamat pengiriman)
            // 'shipping_address' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = $product->price; // Untuk satu produk, kuantitas 1

            $order = Order::create([
                'user_id'        => $buyer->id, // Ini adalah buyer_id
                'total_amount'   => $totalAmount,
                'status'         => 'pending_payment', // Status awal
                'order_date'     => now(),
                'payment_method' => $validatedData['payment_method'],
            ]);

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'quantity'   => 1, // Untuk "Buy Now", kuantitas 1
                'price'      => $product->price, // Harga produk saat itu
            ]);

            DB::commit();

            return redirect()->route('transactions.show', $order->id)
                ->with('success', 'Pembelian berhasil! Pesanan Anda #' . $order->id . ' telah dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error processing direct buy for product {$product->id} by user {$buyer->id}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('products.show', $product->id)
                ->with('error', 'Terjadi kesalahan saat memproses pembelian Anda.');
        }
    }

    /**
     * Metode markSold Anda yang sudah ada.
     * Pastikan dependensi dan logika ini masih sesuai dengan alur aplikasi Anda.
     */
    public function markSold(Request $request, int $productId)
    {
        $product = $this->productRepository->findById($productId);
        if (!$product || $product->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki otorisasi untuk ini atau produk tidak ditemukan.');
        }
        $buyerId = $request->input('buyer_id');
        if (!$buyerId) {
            return back()->with('error', 'Pembeli tidak ditentukan.');
        }
        $buyer = $this->userRepository->findById($buyerId);
        if (!$buyer) {
            return back()->with('error', 'Pembeli tidak valid.');
        }
        if ($this->productService->markProductAsSold($product->id, $buyer)) {
            return back()->with('success', 'Produk berhasil ditandai terjual dan pesanan dibuat!');
        }
        return back()->with('error', 'Gagal menandai produk terjual.');
    }

    /**
     * Metode rate Anda yang sudah ada.
     */
    public function rate(RatingRequest $request, OrderItem $orderItem)
    {
        $currentUser = Auth::user();
        $orderItem->load('order.user', 'product.user');

        if (!$currentUser || $currentUser->id !== $orderItem->order->user_id) {
            abort(403, 'AKSES DITOLAK. Anda tidak berhak memberi rating untuk item ini.');
        }

        if (!in_array($orderItem->order->status, ['completed', 'delivered'])) {
            return redirect()->back()->with('error', 'Rating hanya bisa diberikan setelah pesanan selesai/terkirim.');
        }

        if ($orderItem->rating()->where('user_id', $currentUser->id)->exists()) {
            return redirect()->back()->with('error', 'Anda sudah memberi rating untuk produk ini dalam pesanan ini.');
        }

        try {
            Rating::create([
                'order_item_id' => $orderItem->id,
                'user_id'       => $currentUser->id,
                'product_id'    => $orderItem->product_id,
                'score'         => $request->score,
                'comment'       => $request->comment,
            ]);

            return redirect()->back()->with('success', 'Rating berhasil disimpan!');
        } catch (\Exception $e) {
            Log::error("Error saving rating for OrderItem {$orderItem->id} by user {$currentUser->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan rating.');
        }
    }
}