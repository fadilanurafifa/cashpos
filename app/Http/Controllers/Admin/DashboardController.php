<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function indexPage() 
    {
        // Menghitung total pemasukan dari tabel penjualan
        $totalIncome = DB::table('penjualan')->sum('total_bayar');
        
        // Target pemasukan yang ingin dicapai
        $targetIncome = 10000000; 
        
        // Hitung persentase pencapaian pemasukan
        $incomePercentage = $totalIncome > 0 ? ($totalIncome / $targetIncome) * 100 : 0;
        
        $totalBarang = DB::table('barang')->count();
        $totalPelanggan = DB::table('pelanggan')->count();
        $totalTransaksi = DB::table('penjualan')->count();
        
        $targetTransaksi = 100;
        $persentase = $totalTransaksi > 0 ? ($totalTransaksi / $targetTransaksi) * 100 : 0;
        
        // Ambil data transaksi dan pendapatan berdasarkan hari
        $salesData = DB::table('penjualan')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total_transactions'), DB::raw('SUM(total_bayar) as total_income'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dates = [];
        $transactionsByDay = [];
        $incomeByDay = [];

        foreach ($salesData as $data) {
            $dates[] = $data->date;
            $transactionsByDay[] = $data->total_transactions;
            $incomeByDay[] = $data->total_income;
        }
        
        return view('admin.pages.dashboard.index', compact('totalBarang', 'totalPelanggan', 'totalTransaksi', 'targetTransaksi', 'persentase', 'totalIncome', 'targetIncome', 'incomePercentage', 'dates', 'transactionsByDay', 'incomeByDay'));
    }
}