<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CategoryRepository;
use App\Http\Requests\CategoryRequest;
use App\Models\Category; // Import Model Category
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = $this->categoryRepository->getAllCategories();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(CategoryRequest $request)
    {
        try {
            $this->categoryRepository->create($request->validated());
            return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan kategori: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        try {
            $this->categoryRepository->update($category, $request->validated());
            return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui kategori: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        if ($this->categoryRepository->delete($category->id)) {
            return back()->with('success', 'Kategori berhasil dihapus.');
        }
        return back()->with('error', 'Gagal menghapus kategori.');
    }
}