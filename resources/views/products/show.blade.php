@extends('layouts.app') {{-- Pastikan ini adalah layout utama Anda --}}

@section('title', $product->name) {{-- Menampilkan nama produk sebagai judul halaman --}}

@section('content')
    <div class="container py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->title }}</li> {{-- Ganti $product->name
                menjadi $product->title jika itu yang benar --}}
            </ol>
        </nav>

        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="card-title mb-4">{{ $product->title }}</h1> {{-- Ganti $product->name menjadi $product->title
                jika itu yang benar --}}

                <div class="row">
                    <div class="col-md-7 mb-3 mb-md-0">
                        @if($product->image_url) {{-- Menggunakan accessor image_url seperti di index --}}
                            <img src="{{ $product->image_url }}" class="img-fluid rounded shadow-sm" alt="{{ $product->title }}"
                                style="max-height: 500px; width: 100%; object-fit: cover;">
                        @else
                            <img src="https://placehold.co/800x600/EBF0F5/7F92B0?text=No+Image"
                                class="img-fluid rounded shadow-sm" alt="No Image Available">
                        @endif
                    </div>
                    <div class="col-md-5">
                        <h4 class="text-primary fw-bold mb-3">Rp{{ number_format($product->price, 0, ',', '.') }}</h4>

                        <p class="mb-1"><strong>Kategori:</strong> <a
                                href="{{ route('products.index', ['category' => $product->category->slug ?? '']) }}">{{ $product->category->name ?? 'Tidak ada kategori' }}</a>
                        </p>
                        <p class="mb-1"><strong>Kondisi:</strong>
                            {{ Str::title(str_replace('_', ' ', $product->condition)) }}</p>
                        <p class="mb-1"><strong>Kota:</strong> {{ $product->city }}</p>
                        @if($product->address)
                            <p class="mb-3"><strong>Alamat:</strong> {{ $product->address }}</p>
                        @endif

                        <h5 class="mt-4">Deskripsi Produk:</h5>
                        <p style="white-space: pre-wrap;">{{ $product->description }}</p>

                        {{-- AWAL BAGIAN TOMBOL AKSI YANG DIPERBAIKI --}}
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-grid gap-2">
                            @csrf
                            {{-- Anda bisa menambahkan input untuk kuantitas di sini --}}
                            <div class="input-group mb-2" style="max-width: 120px;">
                                <span class="input-group-text">Qty</span>
                                <input type="number" name="quantity" value="1" min="1" class="form-control form-control-sm">
                            </div>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                            </button>
                        </form>

                        {{-- Tombol Chat Penjual --}}
                        {{-- Pastikan $product->user_id adalah ID penjual --}}
                        @if(Auth::check() && Auth::id() !== $product->user_id) {{-- Jangan tampilkan jika melihat produk
                            sendiri atau belum login --}}
                            <form
                                action="{{ route('chat.startWithSeller', ['seller' => $product->user_id, 'product' => $product->id]) }}"
                                method="POST" class="d-grid">
                                @csrf
                                <button type="submit" class="btn btn-info btn-lg"> {{-- btn-lg untuk membuatnya lebih besar
                                    --}}
                                    <i class="fas fa-comments"></i> Chat Penjual
                                </button>
                            </form>
                        @endif
                    </div>
                    {{-- AKHIR BAGIAN TOMBOL AKSI YANG DIPERBAIKI --}}

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Produk
                        </a>
                        <div>
                            @auth
                                {{-- Tombol Edit dan Hapus hanya jika pengguna adalah pemilik produk atau admin --}}
                                {{-- Anda perlu Gate atau Policy untuk 'update' dan 'delete' Product --}}
                                @can('update', $product)
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-outline-info me-2">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                @endcan
                                @can('delete', $product)
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                        class="d-inline-block" onsubmit="return confirm('Anda yakin ingin menghapus produk ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                @endcan
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        {{-- Kode untuk menampilkan pesan sukses atau error --}}
    

    {{-- Bagian Produk Terkait (Opsional) --}}
    <div class="mt-5">
        <h3>Produk Terkait</h3>
        <div class="row">
            {{-- Loop produk terkait di sini --}}
        </div>
    </div>

    </div>
@endsection

@push('styles')
    <style>
        .breadcrumb-item a {
            text-decoration: none;
        }

        .card-title {
            font-weight: 600;
        }
    </style>
@endpush