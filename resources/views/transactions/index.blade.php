{{-- resources/views/transactions/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Riwayat Pesanan Saya') {{-- Menambahkan judul halaman --}}

@section('content')
<div class="container py-4"> {{-- Menambahkan container untuk layout yang lebih baik --}}
    <h1 class="mb-4">Riwayat Pesanan Anda</h1>

    {{-- Notifikasi (toast) akan muncul secara otomatis jika ada pesan flash dari controller --}}

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">ID Pesanan</th>
                    <th scope="col">Total Jumlah</th>
                    <th scope="col">Status</th>
                    <th scope="col">Tanggal Pesanan</th>
                    <th scope="col">Detail</th>
                </tr>
            </thead>
            <tbody>
                {{-- UBAH $transactions MENJADI $orders --}}
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        {{-- UBAH $transaction->amount MENJADI $order->total_amount dan format Rp --}}
                        <td>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        {{-- UBAH $transaction->status MENJADI $order->status dan gunakan Str::title --}}
                        <td><span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'info') }}">{{ Str::title($order->status) }}</span></td>
                        {{-- UBAH $transaction->created_at MENJADI $order->created_at --}}
                        <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                        {{-- UBAH $transaction MENJADI $order untuk route --}}
                        <td><a href="{{ route('transactions.show', $order->id) }}" class="btn btn-sm btn-outline-primary">Lihat</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada pesanan ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
