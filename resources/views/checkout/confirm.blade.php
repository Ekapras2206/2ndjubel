@extends('layouts.app') {{-- Sesuaikan dengan layout utama Anda --}}

@section('title', 'Konfirmasi Pembelian - ' . $product->title)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Konfirmasi Pembelian Anda</h4>
                </div>
                <div class="card-body p-4">
                    <h2 class="mb-3">{{ $product->title }}</h2>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <img src="{{ $product->first_image_url }}" alt="{{ $product->title }}" class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
                        </div>
                        <div class="col-md-8">
                            <p class="text-muted">{{ Str::limit($product->description, 150) }}</p>
                            <h4>Harga: <span class="text-success fw-bold">Rp{{ number_format($product->price, 0, ',', '.') }}</span></h4>
                            <p class="mb-0"><small>Kategori: {{ $product->category->name ?? 'N/A' }}</small></p>
                            <p class="mb-0"><small>Penjual: {{ $product->user->name ?? 'N/A' }}</small></p>
                        </div>
                    </div>

                    <hr>

                    <form action="{{ route('products.processDirectBuy', $product->id) }}" method="POST">
                        @csrf
                        {{-- Anda bisa menambahkan input hidden lain jika perlu, misal quantity jika bisa diubah --}}
                        {{-- <input type="hidden" name="quantity" value="1"> --}}

                        <div class="mb-3">
                            <label for="payment_method" class="form-label fs-5">Pilih Metode Pembayaran:</label>
                            <select class="form-select form-select-lg @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                <option value="" selected disabled>-- Pilih Metode --</option>
                                <option value="manual_transfer_bca" {{ old('payment_method') == 'manual_transfer_bca' ? 'selected' : '' }}>Transfer Bank BCA</option>
                                <option value="manual_transfer_mandiri" {{ old('payment_method') == 'manual_transfer_mandiri' ? 'selected' : '' }}>Transfer Bank Mandiri</option>
                                <option value="gopay" {{ old('payment_method') == 'gopay' ? 'selected' : '' }}>GoPay</option>
                                <option value="ovo" {{ old('payment_method') == 'ovo' ? 'selected' : '' }}>OVO</option>
                                {{-- Tambahkan metode pembayaran lain sesuai kebutuhan --}}
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info mt-3">
                            <p class="mb-1"><i class="fas fa-info-circle"></i> Anda akan membeli <strong>1 unit</strong> produk ini.</p>
                            <p class="mb-0">Pastikan semua informasi sudah benar sebelum melanjutkan.</p>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check-circle"></i> Konfirmasi Pembelian & Lanjutkan
                            </button>
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    .card-header h4 {
        font-weight: 500;
    }
</style>
@endpush
