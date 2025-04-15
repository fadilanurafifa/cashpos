<?php

namespace App\Http\Controllers;

use App\Exports\ProdukExport;
use App\Models\Produk;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class LaporanProdukController extends Controller
{
    // Menampilkan halaman laporan produk
    public function laporanProduk()
    {
        $produk = Produk::all(); // Ambil semua data produk dari database
        // Log saat halaman laporan produk diakses
        Log::info('Menampilkan laporan produk', [
            'user' => Auth::user()->name ?? 'Guest',
            'time' => now()->toDateTimeString()
        ]);
        return view('admin.laporan-produk.index', compact('produk')); // Kirim data ke tampilan laporan produk
    }

    // Mencetak laporan produk dalam format PDF
    public function cetakLaporanProduk()
    {
        $produk = Produk::all(); // Ambil semua data produk dari database

         // Log saat laporan produk dicetak ke PDF
        Log::info('Mencetak laporan produk ke PDF', [
            'user' => Auth::user()->name ?? 'Guest',
            'time' => now()->toDateTimeString()
        ]);
        return view('admin.laporan-produk.pdf', compact('produk')); // Kirim data ke tampilan PDF laporan produk
    }
    public function exportExcel()
    {
        // Log saat laporan produk diekspor ke Excel
        Log::info('Mengekspor laporan produk ke Excel', [
            'user' => Auth::user()->name ?? 'Guest',
            'time' => now()->toDateTimeString()
        ]);

        return Excel::download(new ProdukExport, 'Laporan_Produk.xlsx');
    }
}
