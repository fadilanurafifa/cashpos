<?php

namespace App\Http\Controllers\Admin; // Menentukan namespace dari controller agar sesuai dengan struktur folder

use App\Http\Controllers\Controller; // Mengimpor base Controller dari Laravel
use Illuminate\Http\Request; // Mengimpor class Request untuk menangani input HTTP
use Illuminate\Support\Facades\DB; // Mengimpor facade DB untuk query database

class DashboardController extends Controller // Mendefinisikan class DashboardController yang merupakan turunan dari Controller
{
    public function indexPage()  // Method untuk menampilkan halaman dashboard
    {
        // Menghitung total pemasukan dari tabel penjualan (jumlah total_bayar)
        $totalIncome = DB::table('penjualan')->sum('total_bayar');
        
        // Menentukan target pemasukan yang ingin dicapai
        $targetIncome = 10000000; 
        
        // Menghitung persentase pemasukan terhadap target
        $incomePercentage = $totalIncome > 0 ? ($totalIncome / $targetIncome) * 100 : 0;
        
        // Menghitung jumlah total barang dari tabel barang
        $totalBarang = DB::table('barang')->count();
        
        // Menghitung jumlah total pelanggan dari tabel pelanggan
        $totalPelanggan = DB::table('pelanggan')->count();
        
        // Menghitung jumlah total transaksi dari tabel penjualan
        $totalTransaksi = DB::table('penjualan')->count();
        
        // Menentukan target jumlah transaksi
        $targetTransaksi = 100;
        
        // Menghitung persentase jumlah transaksi terhadap target
        $persentase = $totalTransaksi > 0 ? ($totalTransaksi / $targetTransaksi) * 100 : 0;
        
        // Mengambil data jumlah transaksi dan pemasukan per hari dari tabel penjualan
        $salesData = DB::table('penjualan')
            ->select(
                DB::raw('DATE(created_at) as date'), // Mengambil tanggal dari field created_at
                DB::raw('COUNT(*) as total_transactions'), // Menghitung jumlah transaksi per hari
                DB::raw('SUM(total_bayar) as total_income') // Menghitung total pemasukan per hari
            )
            ->groupBy('date') // Mengelompokkan data berdasarkan tanggal
            ->orderBy('date') // Mengurutkan data berdasarkan tanggal
            ->get(); // Menjalankan query dan mengambil hasilnya
        
        // Inisialisasi array untuk menyimpan data tanggal, transaksi, dan pemasukan per hari
        $dates = [];
        $transactionsByDay = [];
        $incomeByDay = [];

        // Memasukkan data dari hasil query ke dalam array
        foreach ($salesData as $data) {
            $dates[] = $data->date; // Menyimpan tanggal
            $transactionsByDay[] = $data->total_transactions; // Menyimpan jumlah transaksi
            $incomeByDay[] = $data->total_income; // Menyimpan total pemasukan
        }
        
        // Mengembalikan view dashboard dengan semua data yang sudah dikalkulasi
        return view('admin.pages.dashboard.index', compact(
            'totalBarang', 'totalPelanggan', 'totalTransaksi', 
            'targetTransaksi', 'persentase', 
            'totalIncome', 'targetIncome', 'incomePercentage', 
            'dates', 'transactionsByDay', 'incomeByDay'
        ));
    }
}
