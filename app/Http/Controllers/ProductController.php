<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use App\Http\Requests\ProductRequest;
use App\Models\Product; // Import Model Product
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // Ditambahkan jika belum ada, untuk konsistensi
use Illuminate\Support\Facades\Log; // Ditambahkan untuk logging error di store

class ProductController extends Controller
{
    protected $productService;
    protected $productRepository;
    protected $categoryRepository;

    public function __construct(ProductService $productService, ProductRepository $productRepository, CategoryRepository $categoryRepository)
    {
        $this->productService = $productService;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::approved()->with(['category', 'user']); // Mulai dengan produk yang disetujui, eager load relasi

        // Filter pencarian
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        // Filter kategori
        if ($request->filled('category')) {
            $category = $this->categoryRepository->findBySlug($request->category);
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }
        // Filter kondisi
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }
        // Filter harga
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        // Filter kota (diubah untuk pencocokan persis jika menggunakan dropdown)
        if ($request->filled('city')) {
            // Jika dropdown mengirimkan nama kota yang pasti, gunakan pencocokan persis:
            $query->where('city', $request->city);
            // Jika input kota masih berupa teks bebas dan ingin pencarian 'like', biarkan seperti kode asli Anda:
            // $query->where('city', 'like', '%' . $request->city . '%');
        }

        // Filter Lokasi (menggunakan Haversine formula di query builder)
        // Biarkan ini ada jika Anda masih ingin fungsionalitas ini berjalan jika parameternya ada
        if ($request->filled('latitude') && $request->filled('longitude') && $request->filled('radius')) {
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $radius = $request->radius; // dalam KM

            // Pastikan untuk tidak melakukan select() jika sudah ada select() sebelumnya
            // atau pastikan semua kolom yang dibutuhkan tetap ada.
            // Jika $query sudah memiliki select() lain, DB::raw() ini akan menimpanya.
            // Lebih aman menambahkan kolom distance tanpa menimpa select yang sudah ada.
            $distanceSubQuery = DB::raw("( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) )");
            $query->select('*'); // Pastikan semua kolom asli terpilih
            $query->selectSub($distanceSubQuery, 'distance')
                ->addBinding([$latitude, $longitude, $latitude], 'select') // Tambahkan binding untuk subquery
                ->having('distance', '<', $radius)
                ->orderBy('distance');
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(12); // Paginasi hasil
        $categories = $this->categoryRepository->getAllCategories(); // Untuk dropdown filter kategori

        // ++ AMBIL DAFTAR KOTA YANG UNIK DARI PRODUK ++
        $cities = Product::query() // Ambil dari semua produk agar dropdown lengkap
            ->select('city')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city', 'asc')
            ->pluck('city');

        // Kirim 'cities' ke view
        return view('products.index', compact('products', 'categories', 'cities'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = $this->categoryRepository->getAllCategories();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(ProductRequest $request)
    {
        try {
            $product = $this->productService->uploadProduct(Auth::user(), $request->validated());
            return redirect()->route('products.show', $product->id)->with('success', 'Produk berhasil diunggah dan menunggu verifikasi admin!');
        } catch (\Exception $e) {
            Log::error('Error uploading product: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString()); // Logging error lebih detail
            return back()->withInput()->with('error', 'Gagal mengunggah produk: Terjadi kesalahan pada server.');
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        // Pastikan produk sudah di-approve atau user adalah pemilik produk atau admin
        $isOwner = Auth::check() && Auth::id() === $product->user_id;
        $isAdmin = false;
        if (Auth::check()) {
            $currentUser = Auth::user();
            if ($currentUser instanceof \App\Models\User) { // Pastikan instance User
                $isAdmin = $currentUser->isAdmin();
            }
        }

        if ($product->status !== 'approved' && !$isOwner && !$isAdmin) {
            abort(404, 'Produk tidak ditemukan atau belum diverifikasi.');
        }
        $product->load(['user', 'category']); // Eager load relasi
        return view('products.show', compact('product'));
    }

    // Anda bisa menambahkan method edit, update, destroy di sini jika diperlukan
    // Pastikan otorisasi (hanya pemilik produk yang bisa edit/hapus)
}
