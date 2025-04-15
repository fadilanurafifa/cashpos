<?php

namespace App\Http\Controllers\Admin; // Menentukan namespace dari controller agar sesuai dengan struktur folder

use App\Http\Controllers\Controller; // Mengimpor base Controller dari Laravel
use Illuminate\Http\Request; // Mengimpor class Request untuk menangani input HTTP
use Illuminate\Support\Facades\DB; // Mengimpor facade DB untuk query database
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller // Mendefinisikan class DashboardController yang merupakan turunan dari Controller
{
    public function indexPage()  // Method untuk menampilkan halaman dashboard
    {
        $totalIncome = DB::table('detail_penjualan')
        ->join('produk', 'detail_penjualan.produk_id', '=', 'produk.id')
        ->select(DB::raw('SUM((produk.harga - produk.harga_pokok) * detail_penjualan.jumlah) as total_keuntungan'))
        ->value('total_keuntungan');
    
        // Menentukan target pemasukan yang ingin dicapai
        $targetIncome = 10000000; 
        
        // Menghitung persentase pemasukan terhadap target
        $incomePercentage = $totalIncome > 0 ? ($totalIncome / $targetIncome) * 100 : 0;
        
        // Menghitung jumlah total pelanggan dari tabel pelanggan
        $totalPelanggan = DB::table('pelanggan')->count();
        
        // Menghitung jumlah total transaksi dari tabel penjualan
        $totalTransaksi = DB::table('penjualan')->count();
        
        // Menentukan target jumlah transaksi
        $targetTransaksi = 100;
        
        // Menghitung persentase jumlah transaksi terhadap target
        $persentase = $totalTransaksi > 0 ? ($totalTransaksi / $targetTransaksi) * 100 : 0;
        
        // Mengambil data jumlah transaksi dan pemasukan per hari dari tabel penjualan
        $salesData = DB::table('penjualan') // Mengambil data dari tabel penjualan
        ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.penjualan_id') // Join untuk mengaitkan penjualan dengan detail_penjualan
        ->join('produk', 'detail_penjualan.produk_id', '=', 'produk.id') // Join untuk mengaitkan detail_penjualan dengan data produk
        ->select(
            DB::raw('DATE(penjualan.created_at) as date'), // Mengambil tanggal dari field created_at dan memberi alias 'date'
            DB::raw('COUNT(DISTINCT penjualan.id) as total_transactions'), // Menghitung jumlah transaksi unik per hari
            DB::raw('SUM((produk.harga - produk.harga_pokok) * detail_penjualan.jumlah) as total_income') // Menghitung total keuntungan per hari
        )
        ->groupBy('date') // Mengelompokkan data berdasarkan tanggal
        ->orderBy('date') // Mengurutkan data berdasarkan tanggal secara menaik
        ->get(); // Menjalankan query dan mengambil semua hasilnya dalam bentuk koleksi  
        
          // Logging akses dashboard
            Log::info('Dashboard diakses', [
                'total_keuntungan' => $totalIncome,
                'income_percentage' => $incomePercentage,
                'total_pelanggan' => $totalPelanggan,
                'total_transaksi' => $totalTransaksi,
                'transaksi_percentage' => $persentase,
            ]);
        
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
            'totalPelanggan', 'totalTransaksi', 
            'targetTransaksi', 'persentase', 
            'totalIncome', 'targetIncome', 'incomePercentage', 
            'dates', 'transactionsByDay', 'incomeByDay'
        ));
    }
}
