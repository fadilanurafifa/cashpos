<?php

namespace App\Http\Controllers;

use App\Exports\TransaksiExport;
use Illuminate\Http\Request;
use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class HistoryPenjualanController extends Controller
{
    // Menampilkan riwayat transaksi penjualan
    public function index()
    {
        // Mengambil semua detail transaksi, termasuk informasi produk dan pelanggan terkait
        $detailTransaksi = DetailPenjualan::with(['produk', 'penjualan.pelanggan']) // Memuat relasi produk dan pelanggan
            ->get() // Mengambil semua data dari tabel DetailPenjualan
            ->groupBy('penjualan_id'); // Mengelompokkan detail transaksi berdasarkan ID penjualan
    
        // Mengambil data transaksi utama dengan informasi pelanggan terkait
        $transaksi = Penjualan::select([
            'id',
            'pelanggan_id',
            'total_bayar',
            'status_pembayaran',
            'created_at',
            'kasir_slot', // ✅ Tambahkan ini
            'kasir_nama'  // ✅ Tambahkan ini
        ])
        ->with('pelanggan')
        ->get()
        ->keyBy('id');
    
    
            $penjualan = Penjualan::with('detailTransaksi.produk')->get();
            
        // Menampilkan view 'admin.history-penjualan.index' dengan data transaksi dan detail transaksi
        return view('admin.history-penjualan.index', compact('detailTransaksi', 'transaksi'));

    }
}
