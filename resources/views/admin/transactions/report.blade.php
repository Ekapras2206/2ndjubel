@extends('admin') {{-- Sesuaikan dengan layout admin Anda --}}

@section('page_title', 'Transaction Reports')

@section('content')
<div class="container-fluid px-4">
    {{-- <h1 class="mt-4">Laporan Transaksi</h1> --}}
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Laporan Transaksi</li>
    </ol>

    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filter Laporan
        </div>
        <div class="card-body">
            <form action="{{ route('admin.transactions.report') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="start_date" class="form-label">Tanggal Mulai:</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-5">
                        <label for="end_date" class="form-label">Tanggal Akhir:</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-1"></i> Terapkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Daftar Semua Transaksi
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">ID Transaksi</th>
                            <th scope="col">Pembeli</th> {{-- Mengganti 'User' menjadi 'Pembeli' untuk kejelasan --}}
                            <th scope="col">Total Harga</th> {{-- Mengganti 'Amount' menjadi 'Total Harga' dan format Rupiah --}}
                            <th scope="col">Status</th>
                            <th scope="col">Tanggal Transaksi</th> {{-- Mengganti 'Date' menjadi 'Tanggal Transaksi' --}}
                            <th scope="col">Aksi</th> {{-- Menambahkan kolom Aksi untuk tombol Detail --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr>
                                <td>#{{ $transaction->id }}</td>
                                <td>{{ $transaction->buyer->name ?? ($transaction->user->name ?? 'N/A') }}</td> {{-- Menggunakan relasi buyer atau user --}}
                                <td>Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</td> {{-- Menggunakan total_amount dan format Rupiah --}}
                                <td>
                                    <span class="badge
                                        @if(in_array($transaction->status, ['completed', 'paid', 'shipped'])) bg-success
                                        @elseif(in_array($transaction->status, ['pending_payment', 'pending'])) bg-warning text-dark
                                        @elseif(in_array($transaction->status, ['cancelled', 'failed', 'refunded'])) bg-danger
                                        @else bg-secondary @endif">
                                        {{ Str::title(str_replace('_', ' ', $transaction->status)) }}
                                    </span>
                                </td>
                                <td>{{ $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y, H:i') : $transaction->created_at->format('d M Y, H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.transactions.show', $transaction->id) }}" class="btn btn-sm btn-info"> {{-- Asumsi ada rute admin.transactions.show --}}
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada transaksi ditemukan untuk periode yang dipilih atau belum ada transaksi sama sekali.</td> {{-- Colspan disesuaikan --}}
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transactions->hasPages())
            <div class="card-footer clearfix">
                {{ $transactions->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- <div class="card shadow-sm">
        <div class="card-body bg-light">
            <h3 class="mb-0 text-end">Total Pendapatan (dari transaksi yang difilter):
                <span class="fw-bold text-success">Rp{{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</span> {{-- Format Rupiah --}}
            {{-- </h3>
        </div>
    </div> --}}
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush
