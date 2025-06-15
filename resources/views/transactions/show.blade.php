@extends('layouts.app') {{-- Sesuaikan dengan layout utama Anda --}}

@section('title', 'Detail Pesanan #' . $order->id)

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('transactions.index') }}">Riwayat Pesanan</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail Pesanan #{{ $order->id }}</li>
        </ol>
    </nav>

    <h1 class="mb-4">Detail Pesanan #{{ $order->id }}</h1>

    {{-- Notifikasi akan muncul di sini jika ada dari layouts/app.blade.php --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">Informasi Pesanan</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3 mb-md-0">
                    <p><strong>ID Pesanan:</strong> #{{ $order->id }}</p>
                    <p><strong>Tanggal Pesanan:</strong> {{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d M Y, H:i') : ($order->created_at ? $order->created_at->format('d M Y, H:i') : 'N/A') }}</p>
                    <p><strong>Status:</strong>
                        <span class="badge
                            @if(in_array($order->status, ['pending_payment', 'pending'])) bg-warning text-dark
                            @elseif(in_array($order->status, ['paid', 'completed', 'delivered', 'shipped'])) bg-success
                            @elseif(in_array($order->status, ['cancelled', 'failed', 'refunded'])) bg-danger
                            @else bg-secondary @endif">
                            {{ Str::title(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </p>
                    <p><strong>Pembeli:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Total Pesanan:</strong> <span class="fw-bold fs-5 text-primary">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span></p>
                    <p><strong>Metode Pembayaran:</strong> {{ Str::title(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}</p>
                    <p><strong>Alamat Pengiriman:</strong> {{ $order->shipping_address ?? 'N/A' }}</p> {{-- Asumsi ada kolom ini di tabel orders --}}
                    <p><strong>Nomor Telepon:</strong> {{ $order->phone_number ?? ($order->user->phone_number ?? 'N/A') }}</p> {{-- Asumsi ada kolom ini di tabel orders atau user --}}
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Produk dalam Pesanan</h5>
        </div>
        <div class="card-body p-0">
            @if($order->items && $order->items->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 10%;">Gambar</th>
                                <th scope="col">Nama Produk</th>
                                <th scope="col" class="text-end">Harga Satuan</th>
                                <th scope="col" class="text-center">Kuantitas</th>
                                <th scope="col" class="text-end">Subtotal</th>
                                <th scope="col" class="text-center">Aksi</th> {{-- Kolom Aksi untuk Rating --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>
                                        @if($item->product && $item->product->first_image_url)
                                            <img src="{{ $item->product->first_image_url }}" alt="{{ $item->product->title }}" class="img-fluid rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                            <img src="https://placehold.co/60x60/EBF0F5/7F92B0?text=N/A" alt="No Image" class="img-fluid rounded">
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->product)
                                            <a href="{{ route('products.show', $item->product->id) }}">{{ $item->product->title }}</a>
                                            <br><small class="text-muted">Penjual: {{ $item->product->user->name ?? 'N/A' }}</small>
                                        @else
                                            Produk tidak tersedia
                                        @endif
                                    </td>
                                    <td class="text-end">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($item->product) {{-- Hanya tampilkan jika produk ada --}}
                                            @php
                                                $isBuyer = Auth::check() && Auth::id() === $order->user_id;
                                                // Sesuaikan status selesai/terkirim yang memungkinkan rating
                                                $isOrderCompleted = in_array($order->status, ['completed', 'delivered']);
                                                // Cek apakah item ini sudah diberi rating oleh user ini
                                                // Asumsi OrderItem model punya relasi rating()
                                                $hasRated = $item->rating()->where('user_id', Auth::id())->exists();
                                            @endphp

                                            @if($isBuyer && $isOrderCompleted && !$hasRated)
                                                <button type="button" class="btn btn-sm btn-info"
                                                    data-bs-toggle="modal" data-bs-target="#rateModal"
                                                    data-order-item-id="{{ $item->id }}"
                                                    data-product-title="{{ $item->product->title }}">
                                                    <i class="fas fa-star"></i> Beri Rating
                                                </button>
                                            @elseif($hasRated)
                                                <span class="badge bg-success"><i class="fas fa-check"></i> Sudah Dinilai</span>
                                                {{-- Anda bisa menampilkan skor ratingnya di sini jika mau --}}
                                                {{-- <p class="text-muted small mt-1">Skor: {{ $item->rating->score ?? 'N/A' }}</p> --}}
                                            @elseif($isBuyer && !$isOrderCompleted)
                                                 <span class="badge bg-secondary">Rating setelah selesai</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center p-3">Tidak ada produk dalam pesanan ini.</p>
            @endif
        </div>
        <div class="card-footer text-end bg-light">
            <strong class="fs-5">Total Keseluruhan: Rp{{ number_format($order->total_amount, 0, ',', '.') }}</strong>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Riwayat Pesanan
        </a>
    </div>
</div>

{{-- Modal untuk Rating --}}
<div class="modal fade" id="rateModal" tabindex="-1" aria-labelledby="rateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rateModalLabel">Beri Rating untuk <strong id="modalProductTitle"></strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="ratingForm" method="POST"> {{-- Action akan diisi oleh JavaScript --}}
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="order_item_id" id="modalOrderItemId">
                    <div class="mb-3">
                        <label for="ratingScore" class="form-label">Skor Rating (1-5)</label>
                        <select class="form-select" id="ratingScore" name="score" required>
                            <option value="" disabled selected>-- Pilih Skor --</option>
                            <option value="5">5 Bintang - Sangat Bagus</option>
                            <option value="4">4 Bintang - Bagus</option>
                            <option value="3">3 Bintang - Cukup</option>
                            <option value="2">2 Bintang - Buruk</option>
                            <option value="1">1 Bintang - Sangat Buruk</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="ratingComment" class="form-label">Komentar (Opsional)</label>
                        <textarea class="form-control" id="ratingComment" name="comment" rows="3" maxlength="500" placeholder="Tulis ulasan Anda di sini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Rating</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    .breadcrumb-item a {
        text-decoration: none;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rateModalElement = document.getElementById('rateModal');
        if (rateModalElement) {
            rateModalElement.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const orderItemId = button.dataset.orderItemId;
                const productTitle = button.dataset.productTitle;

                const modalTitleSpan = rateModalElement.querySelector('#modalProductTitle');
                const modalOrderItemIdInput = rateModalElement.querySelector('#modalOrderItemId');
                const ratingForm = rateModalElement.querySelector('#ratingForm');

                if (modalTitleSpan) modalTitleSpan.textContent = productTitle;
                if (modalOrderItemIdInput) modalOrderItemIdInput.value = orderItemId;
                if (ratingForm) {
                    // Pastikan rute ini sudah didefinisikan di web.php
                    // Contoh: Route::post('/order-items/{orderItem}/rate', [TransactionController::class, 'rate'])->name('orderItems.rate');
                    ratingForm.action = `/order-items/${orderItemId}/rate`;
                }
            });

            rateModalElement.addEventListener('hidden.bs.modal', function () {
                const ratingForm = document.getElementById('ratingForm');
                if (ratingForm) {
                    ratingForm.reset();
                    ratingForm.action = '';
                }
            });
        }
    });
</script>
@endpush
