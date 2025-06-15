{{-- resources/views/cart/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Keranjang Belanja Anda</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(empty($cart))
        <div class="alert alert-info text-center">
            Keranjang belanja Anda kosong.
            <a href="{{ route('products.index') }}" class="alert-link">Mulai berbelanja sekarang!</a>
        </div>
    @else
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center">Gambar</th>
                                <th scope="col">Produk</th>
                                <th scope="col">Harga</th>
                                <th scope="col" class="text-center">Kuantitas</th>
                                <th scope="col" class="text-end">Total</th>
                                <th scope="col" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $grandTotal = 0; @endphp
                            @foreach($cart as $productId => $item)
                                @php
                                    $itemTotal = $item['price'] * $item['quantity'];
                                    $grandTotal += $itemTotal;
                                @endphp
                                <tr>
                                    <td class="text-center" style="width: 100px;">
                                        @if($item['image'])
                                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="img-fluid rounded" style="max-height: 80px; object-fit: cover;">
                                        @else
                                            <img src="https://placehold.co/80x80/EBF0F5/7F92B0?text=No+Image" alt="No Image" class="img-fluid rounded">
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('products.show', $item['product_id']) }}" class="text-decoration-none text-dark fw-bold">
                                            {{ $item['name'] }}
                                        </a>
                                    </td>
                                    <td>Rp{{ number_format($item['price'], 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('cart.update', $productId) }}" method="POST" class="d-flex align-items-center justify-content-center">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control form-control-sm text-center" style="width: 70px;" onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td class="text-end fw-bold">Rp{{ number_format($itemTotal, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('cart.remove', $productId) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini dari keranjang?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total Belanja:</th>
                                <th colspan="2" class="text-end text-primary fs-5">Rp{{ number_format($grandTotal, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Lanjutkan Belanja</a>
                    <div>
                        <form action="{{ route('cart.clear') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning me-2" onclick="return confirm('Yakin ingin mengosongkan seluruh keranjang?');">Kosongkan Keranjang</button>
                        </form>
                        <a href="{{ route('checkout.index') }}" class="btn btn-success">Lanjutkan ke Checkout</a> {{-- Ganti dengan rute checkout Anda --}}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection