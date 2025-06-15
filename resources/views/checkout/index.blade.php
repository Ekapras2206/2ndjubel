{{-- resources/views/checkout/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4">Halaman Checkout</h1>

        {{-- Notifikasi (toast) akan muncul secara otomatis jika ada pesan flash dari controller --}}

        @if(empty($cart))
            <div class="alert alert-info text-center">
                Keranjang belanja Anda kosong. Tidak ada yang perlu di-checkout.
                <a href="{{ route('products.index') }}" class="alert-link">Mulai berbelanja!</a>
            </div>
        @else
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        Detail Pesanan Anda
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-center">Kuantitas</th>
                                        <th class="text-end">Harga Satuan</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart as $item)
                                        <tr>
                                            <td>{{ $item['name'] }}</td>
                                            <td class="text-center">{{ $item['quantity'] }}</td>
                                            <td class="text-end">Rp{{ number_format($item['price'], 0, ',', '.') }}</td>
                                            <td class="text-end">Rp{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total Belanja:</th>
                                        <th class="text-end text-primary fs-5">Rp{{ number_format($grandTotal, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <hr>

                        <h3>Informasi Pengiriman</h3>
                        <p>Di sini Anda akan menambahkan form untuk alamat pengiriman dan detail kontak.</p>
                        <form action="{{ route('checkout.process') }}" method="POST"> @csrf {{--
                                Contoh field --}} <div class="mb-3">
                                <label for="address" class="form-label">Alamat Lengkap</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Nomor Telepon</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>

                    {{-- Tambahkan field lain seperti kota, kode pos, dll. --}}

                    <h3 class="mt-4">Metode Pembayaran</h3>
                    <p>Di sini Anda akan menambahkan pilihan metode pembayaran.</p>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="payment_method" id="bankTransfer" value="bank_transfer"
                            checked>
                        <label class="form-check-label" for="bankTransfer">
                            Transfer Bank
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod">
                        <label class="form-check-label" for="cod">
                            Cash On Delivery (COD)
                        </label>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">Konfirmasi Pesanan</button>
                    </div>
                    </form>
                </div>
            </div>
        @endif

    <div class="mt-3">
        <a href="{{ route('cart.show') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke
            Keranjang</a>
    </div>
    </div>
@endsection