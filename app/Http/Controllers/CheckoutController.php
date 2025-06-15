<?php

namespace App\Http\Controllers;

use App\Models\Order;      // <-- Import Model Order
use App\Models\OrderItem;  // <-- Import Model OrderItem
use App\Models\Product; // Pastikan Model Product diimport
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth; // Jika Anda perlu mengecek otentikasi di sini

class CheckoutController extends Controller
{
    /**
     * Menampilkan form konfirmasi untuk pembelian langsung ("Buy Now").
     * Rute: GET /checkout/confirm-buy/{product}
     * Name: checkout.confirmBuyNow
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\View\View
     */

    public function index()
    {
        $cart = Session::get('cart', []);

        // Jika keranjang kosong, redirect kembali ke halaman keranjang atau produk
        if (empty($cart)) {
            return redirect()->route('cart.show')->with('error', 'Keranjang belanja Anda kosong, tidak dapat melanjutkan ke checkout.');
        }

        // Hitung total belanja (grand total) lagi untuk ditampilkan di halaman checkout
        $grandTotal = 0;
        foreach ($cart as $item) {
            $grandTotal += $item['price'] * $item['quantity'];
        }

        return view('checkout.index', compact('cart', 'grandTotal'));
    }

    public function showConfirmBuyNowForm(Product $product)
    {
        // Pastikan pengguna sudah login untuk mengakses halaman ini
        // Middleware 'auth' pada grup rute seharusnya sudah menangani ini,
        // tapi bisa ditambahkan pengecekan eksplisit jika perlu.
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Anda harus login untuk melanjutkan pembelian.');
        }

        // Anda bisa menambahkan logika tambahan di sini jika perlu, misalnya:
        // 1. Memeriksa apakah produk masih tersedia (stok, status, dll.)
        // if ($product->status !== 'approved' || $product->stock < 1) {
        //     return redirect()->route('products.show', $product->id)
        //                      ->with('error', 'Produk ini tidak tersedia untuk dibeli saat ini.');
        // }

        // 2. Memeriksa apakah pengguna mencoba membeli produknya sendiri
        // if ($product->user_id === Auth::id()) {
        //     return redirect()->route('products.show', $product->id)
        //                      ->with('error', 'Anda tidak dapat membeli produk Anda sendiri.');
        // }

        // Kirim data produk ke view konfirmasi
        return view('checkout.confirm_buy', compact('product'));
    }
    public function process(Request $request)
    {
        // 1. Validasi data dari form
        $request->validate([
            'address'        => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'payment_method' => 'required|in:bank_transfer,cod', // Sesuaikan dengan metode pembayaran Anda
            // Anda bisa menambahkan validasi untuk city, postal_code, dll.
        ]);

        // 2. Ambil data keranjang dari sesi
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.show')->with('error', 'Keranjang belanja Anda kosong, tidak dapat melanjutkan ke checkout.');
        }

        // 3. Hitung ulang total belanja yang akurat dari item di keranjang (penting untuk keamanan)
        $grandTotal = 0;
        foreach ($cart as $productId => $item) {
            // Opsional: Anda bisa memverifikasi harga produk dari database lagi di sini
            // $productDb = Product::find($productId);
            // if (!$productDb || $productDb->price !== $item['price']) {
            //     return redirect()->back()->with('error', 'Harga produk tidak valid.');
            // }
            $grandTotal += $item['price'] * $item['quantity'];
        }

        // 4. Buat entri Order baru di database
        try {
            $order = Order::create([
                'user_id'          => Auth::check() ? Auth::id() : null, // ID pengguna yang login, atau null jika guest
                'total_amount'     => $grandTotal,
                'shipping_address' => $request->address,
                'phone_number'     => $request->phone,
                'payment_method'   => $request->payment_method,
                'status'           => 'pending', // Status awal pesanan
            ]);

            // 5. Buat entri OrderItem untuk setiap produk di keranjang
            foreach ($cart as $productId => $item) {
                $order->items()->create([ // Menggunakan relasi 'items' dari model Order
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'], // Simpan harga saat pesanan dibuat
                ]);
            }

            // 6. Kosongkan keranjang setelah pesanan berhasil disimpan
            Session::forget('cart');

            // 7. Redirect ke halaman konfirmasi pesanan atau halaman utama dengan pesan sukses
            return redirect()->route('home')->with('success', 'Pesanan Anda telah berhasil ditempatkan! ID Pesanan Anda: ' . $order->id);
            // Anda mungkin ingin redirect ke route('order.confirmation', $order->id)
            // setelah membuat rute dan view untuk konfirmasi pesanan.

        } catch (\Exception $e) {
            // Tangani error jika terjadi masalah saat menyimpan ke database
            // Log error untuk debugging
            Log::error("Error saat memproses checkout: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pesanan Anda. Silakan coba lagi.');
        }
    }
    public function processBuyNow(Request $request, Product $product)
    {
        $request->validate([
            'address'        => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'payment_method' => 'required|in:bank_transfer,cod',
        ]);

        // Cegah pengguna membeli produknya sendiri
        if ($product->user_id === Auth::id()) {
            return redirect()->route('products.show', $product->id)
                ->with('error', 'Anda tidak dapat membeli produk Anda sendiri.');
        }

        // Cek ketersediaan (jika pakai stok atau status)
        if ($product->status !== 'approved') {
            return redirect()->route('products.show', $product->id)
                ->with('error', 'Produk ini tidak tersedia untuk dibeli.');
        }

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id'          => Auth::id(),
                'total_amount'     => $product->price,
                'shipping_address' => $request->address,
                'phone_number'     => $request->phone,
                'payment_method'   => $request->payment_method,
                'status'           => 'pending',
            ]);

            $order->items()->create([
                'product_id' => $product->id,
                'quantity'   => 1, // Buy Now selalu 1 item
                'price'      => $product->price,
            ]);

            DB::commit();
            return redirect()->route('home')->with('success', 'Pembelian berhasil! ID Pesanan Anda: ' . $order->id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Buy Now: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pesanan.');
        }
    }
}
