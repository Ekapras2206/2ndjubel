<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Repositories\TransactionRepository;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile; // Import UploadedFile

class ProductService
{
    public $productRepository;
    public $transactionRepository;

    public function __construct(ProductRepository $productRepository, TransactionRepository $transactionRepository)
    {
        $this->productRepository = $productRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function uploadProduct(User $user, array $data): Product
    {
        $imagePathToStore = null;

        // $data['image'] sekarang adalah objek UploadedFile tunggal dari ProductRequest
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $imageFile = $data['image'];
            $path = $imageFile->store('products', 'public'); // Simpan ke storage/app/public/products
            $imagePathToStore = $path; // Path akan jadi 'products/namafileunik.jpg'
        }

        // Salin data asli untuk dimodifikasi
        $dataToCreate = $data;
        // Hapus 'image' (objek UploadedFile) karena kita akan menyimpan path-nya di kolom 'images'
        unset($dataToCreate['image']);

        $dataToCreate['user_id'] = $user->id;
        $dataToCreate['images'] = $imagePathToStore; // Simpan path string tunggal ke kolom 'images'
        $dataToCreate['status'] = 'pending';

        return $this->productRepository->create($dataToCreate);
    }

    // ... (metode approveProduct, rejectProduct, markProductAsSold Anda tetap sama) ...
    public function approveProduct(int $productId): bool {
        $product = $this->productRepository->findById($productId);
        if (!$product) return false;
        $product->approve();
        return true;
    }

    public function rejectProduct(int $productId, ?string $reason = null): bool {
        $product = $this->productRepository->findById($productId);
        if (!$product) return false;
        $product->reject();
        return true;
    }

    public function markProductAsSold(int $productId, User $buyer): bool {
        return DB::transaction(function () use ($productId, $buyer) {
            $product = $this->productRepository->findById($productId);
            if (!$product || $product->isSold() || $product->user_id === $buyer->id) return false;
            $product->status = 'sold';
            $product->save();
            $this->transactionRepository->create([
                'seller_id' => $product->user_id,
                'buyer_id' => $buyer->id,
                'product_id' => $productId,
                'final_price' => $product->price,
                'status' => 'completed',
                'transaction_date' => now(),
            ]);
            return true;
        });
    }
}
