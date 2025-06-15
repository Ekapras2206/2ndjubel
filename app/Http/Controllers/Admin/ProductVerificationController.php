<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Models\Product; // Import Model Product
use Illuminate\Http\Request;

class ProductVerificationController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a listing of products pending verification.
     */
    public function index()
    {
        // Mengambil produk yang pending, dan langsung menamakannya productsToVerify
        // Idealnya, ProductService memiliki metode seperti getPendingVerificationProducts()
        // yang akan memanggil $this->productRepository->getPendingProducts()
        // Untuk saat ini, kita akan asumsikan ProductService mengekspos repository
        // atau metode getPendingProducts() ada di ProductService itu sendiri.

        // Jika ProductService memiliki metode untuk ini:
        // $productsToVerify = $this->productService->getPendingProductsForVerification();

        // Jika Anda tetap mengakses repository melalui service seperti kode asli Anda,
        // dan metode getPendingProducts() ada di ProductRepository:
        $productsToVerify = $this->productService->productRepository->getPendingProducts();

        return view('admin.products.verification', compact('productsToVerify'));
    }

    /**
     * Approve a product.
     */
    public function approve(Product $product)
    {
        // Idealnya, ProductService memiliki metode seperti approveProductById(int $productId)
        // atau approveProduct(Product $product)
        if ($this->productService->approveProduct($product->id)) { // Asumsi approveProduct menerima ID
            return back()->with('success', 'Produk berhasil diverifikasi.');
        }
        return back()->with('error', 'Gagal memverifikasi produk.');
    }

    /**
     * Reject a product.
     */
    public function reject(Request $request, Product $product)
    {
        $reason = $request->input('reason'); // Ambil alasan dari form

        // Idealnya, ProductService memiliki metode seperti rejectProductById(int $productId, ?string $reason)
        // atau rejectProduct(Product $product, ?string $reason)
        if ($this->productService->rejectProduct($product->id, $reason)) { // Asumsi rejectProduct menerima ID dan alasan
            return back()->with('success', 'Produk berhasil ditolak.');
        }
        return back()->with('error', 'Gagal menolak produk.');
    }
}
