<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction; // Pastikan Model Transaction diimport
use App\Models\User;       // Pastikan Model User diimport
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Untuk fungsi agregasi jika diperlukan

class TransactionReportController extends Controller
{
    /**
     * Menampilkan laporan transaksi dengan filter tanggal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Transaction::query()->with('user'); // Eager load relasi 'user' (pembeli)

        // Filter berdasarkan tanggal mulai
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        // Filter berdasarkan tanggal akhir
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Ambil semua transaksi yang sudah difilter (sebelum paginasi) untuk menghitung total revenue
        $filteredTransactionsForRevenue = $query->get();
        $totalRevenue = $filteredTransactionsForRevenue->sum('total_amount'); // Asumsi kolomnya 'total_amount'

        // Lanjutkan query untuk paginasi
        $transactions = $query->orderBy('created_at', 'desc')->paginate(15); // Paginasi hasil

        return view('admin.transactions.report', compact('transactions', 'totalRevenue'));
    }
}
