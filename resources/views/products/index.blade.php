@extends('layouts.app')

@section('title', 'Product List')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Product List</h1>

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <a href="{{ route('products.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Create New Product
        </a>
        {{-- Notifikasi --}}
    </div>

    <form action="{{ route('products.index') }}" method="GET" class="mb-4 p-3 border rounded bg-light shadow-sm">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" id="search" class="form-control"
                    placeholder="Title or description..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label for="category" class="form-label">Category</label>
                <select name="category" id="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                {{-- AWAL PERUBAHAN INPUT KOTA MENJADI DROPDOWN --}}
                <label for="city_filter" class="form-label">City</label>
                <select name="city" id="city_filter" class="form-select">
                    <option value="">All Cities</option>
                    {{-- Pastikan variabel $cities dikirim dari ProductController --}}
                    @if(isset($cities) && $cities->count() > 0)
                        @foreach($cities as $cityValue)
                            <option value="{{ $cityValue }}" {{ request('city') == $cityValue ? 'selected' : '' }}>
                                {{ $cityValue }}
                            </option>
                        @endforeach
                    @endif
                </select>
                {{-- AKHIR PERUBAHAN INPUT KOTA MENJADI DROPDOWN --}}
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" type="submit">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </div>
    </form>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        @forelse($products as $product)
            <div class="col">
                <div class="card h-100 shadow-sm product-card">
                    <a href="{{ route('products.show', $product) }}">
                        <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->title }}"
                            style="height: 200px; object-fit: cover;">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-1">
                            <a href="{{ route('products.show', $product) }}"
                                class="text-decoration-none product-title-link">{{ Str::limit($product->title, 45) }}</a>
                        </h5>
                        <p class="card-text text-muted small mb-2 flex-grow-1">{{ Str::limit($product->description, 60) }}
                        </p>
                        <p class="card-text fw-bold text-primary fs-5 mb-2">
                            Rp{{ number_format($product->price, 0, ',', '.') }}</p>

                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-star text-warning me-1"></i>
                            <span class="fw-bold text-dark">{{ number_format($product->average_rating, 1) }}</span>
                            <small class="text-muted ms-1">({{ $product->ratings_count ?? 0 }} reviews)</small>
                        </div>

                        <small class="text-muted">
                            <i class="fas fa-map-marker-alt me-1"></i>{{ $product->city ?? 'N/A' }}
                        </small>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pb-3">
                        <div class="d-grid gap-2">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary">View
                                Details</a>
                            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-grid">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success add-to-cart-btn">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                            </form>
                            <a href="{{ route('checkout.confirmBuyNow', $product->id) }}" class="btn btn-sm btn-danger buy-now-btn">
                                <i class="fas fa-shopping-bag"></i> Buy Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center" role="alert">
                    No products found matching your criteria.
                </div>
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $products->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .product-title-link { color: #212529; }
        .product-title-link:hover { color: #0d6efd; }
        .card-img-top { border-bottom: 1px solid #eee; }
    </style>
@endpush

@push('scripts')
    {{-- Script Anda yang sudah ada bisa tetap di sini --}}
@endpush
