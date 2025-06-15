<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB; // Untuk contoh query lokasi

class ProductRepository
{
    /**
     * Get all approved products.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllApprovedProducts(): Collection
    {
        return Product::approved()->get();
    }

    /**
     * Find a product by its ID.
     *
     * @param int $id
     * @return \App\Models\Product|null
     */
    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }

    /**
     * Create a new product.
     *
     * @param array $data
     * @return \App\Models\Product
     */
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    /**
     * Update an existing product.
     *
     * @param \App\Models\Product $product
     * @param array $data
     * @return bool
     */
    public function update(Product $product, array $data): bool
    {
        return $product->update($data);
    }

    /**
     * Get all products pending verification.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingProducts(): Collection
    {
        return Product::pendingVerification()->get();
    }

    /**
     * Get products near a specific location with optional filters.
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $radiusInKm
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProductsNear(float $latitude, float $longitude, int $radiusInKm, array $filters = []): Collection
    {
        $query = Product::approved();

        // Implementasi filter tambahan
        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (isset($filters['condition'])) {
            $query->where('condition', $filters['condition']);
        }
        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }
        if (isset($filters['city'])) {
            $query->where('city', 'like', '%' . $filters['city'] . '%');
        }

        // Kalkulasi jarak menggunakan Haversine formula (MySQL)
        // Ini akan menambahkan kolom 'distance' ke hasil query
        $query->select(DB::raw("*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance"))
              ->setBindings([$latitude, $longitude, $latitude])
              ->having('distance', '<', $radiusInKm)
              ->orderBy('distance');

        return $query->get();
    }

    /**
     * Delete a product by its ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $product = $this->findById($id);
        if ($product) {
            return $product->delete(); // Soft delete
        }
        return false;
    }
}