<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPenjualanExport;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\DetailPenjualan;
use App\Models\Kategori;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPenjualanController extends Controller
{
    // Menampilkan halaman laporan penjualan
    public function index(Request $request)
    {
        $kategori_id = $request->input('kategori_id'); // Ambil kategori yang dipilih dari request

        // Log request kategori yang diterima
        Log::info('Menampilkan laporan penjualan', [
            'user' => Auth::user()->name ?? 'Guest',
            'kategori_id' => $kategori_id ?? 'Tidak ada filter kategori',
            'time' => now()->toDateTimeString()
        ]);
    
        // Ambil daftar kategori untuk dropdown filter
        $kategoriList = Kategori::all();
    
        // Query produk dengan relasi detailPenjualan, lalu filter berdasarkan kategori jika ada
        $laporan = Produk::with('detailPenjualan')
            ->when($kategori_id, function ($query) use ($kategori_id) {
                return $query->where('kategori_id', $kategori_id); // Jika ada kategori yang dipilih, filter data berdasarkan kategori
            })
            ->get();
    
        return view('admin.laporan.index', compact('laporan', 'kategoriList')); // Kirim data ke tampilan laporan
    }

    // Mencetak laporan penjualan dalam format PDF
    public function cetakPDF(Request $request)
    {
        $kategori_id = $request->kategori_id; // Ambil kategori yang dipilih dari request

        // Log request cetak PDF
        Log::info('Mencetak laporan penjualan ke PDF', [
            'user' => Auth::user()->name ?? 'Guest',
            'kategori_id' => $kategori_id ?? 'Tidak ada filter kategori',
            'time' => now()->toDateTimeString()
        ]);
        
        // Query produk dengan relasi detailPenjualan, lalu filter berdasarkan kategori jika ada
        $laporan = Produk::with(['detailPenjualan'])
            ->when($kategori_id, function ($query) use ($kategori_id) {
                return $query->where('kategori_id', $kategori_id);
            })
            ->get();
    
        foreach ($laporan as $produk) {
            $stok_awal = 100; // Angka stok awal default (bisa disesuaikan jika ada penyimpanan stok)
            $terjual = $produk->detailPenjualan()
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]) // Hitung jumlah produk terjual dalam bulan ini
                ->sum('jumlah');
    
            $produk->stok_awal = $stok_awal; // Simpan stok awal ke dalam objek produk
            $produk->terjual = $terjual; // Simpan jumlah produk yang terjual
            $produk->keuntungan = $terjual * ($produk->harga ?? 0); // Hitung keuntungan berdasarkan jumlah terjual dan harga produk
        }
    
        return view('admin.laporan.cetak', compact('laporan'))->render(); // Kembalikan tampilan HTML dari laporan untuk dicetak
    }
    public function exportExcel(Request $request)
    {
        $kategori_id = $request->kategori_id;

         // Log request untuk ekspor Excel
        Log::info('Mengekspor laporan penjualan ke Excel', [
            'user' => Auth::user()->name ?? 'Guest',
            'kategori_id' => $kategori_id ?? 'Tidak ada filter kategori',
            'time' => now()->toDateTimeString()
        ]);
        return Excel::download(new LaporanPenjualanExport($kategori_id), 'Laporan_Penjualan.xlsx');
    }
}
