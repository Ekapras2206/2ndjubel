<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    /**
     * Get all categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllCategories(): Collection
    {
        return Category::all();
    }

    /**
     * Find a category by its ID.
     *
     * @param int $id
     * @return \App\Models\Category|null
     */
    public function findById(int $id): ?Category
    {
        return Category::find($id);
    }

    /**
     * Find a category by its slug.
     *
     * @param string $slug
     * @return \App\Models\Category|null
     */
    public function findBySlug(string $slug): ?Category
    {
        return Category::where('slug', $slug)->first();
    }

    /**
     * Create a new category.
     *
     * @param array $data
     * @return \App\Models\Category
     */
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Update an existing category.
     *
     * @param \App\Models\Category $category
     * @param array $data
     * @return bool
     */
    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    /**
     * Delete a category.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $category = $this->findById($id);
        if ($category) {
            return $category->delete();
        }
        return false;
    }
}