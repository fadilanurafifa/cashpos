<?php

namespace App\Http\Controllers; // Menentukan namespace controller utama

use App\Exports\TransaksiExport; // Import class untuk export ke Excel
use Illuminate\Http\Request; // Digunakan untuk menangani request HTTP
use App\Models\DetailPenjualan; // Import model DetailPenjualan
use App\Models\Penjualan; // Import model Penjualan
use Barryvdh\DomPDF\Facade\Pdf; // Import facade PDF untuk export PDF
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel; // Import facade Excel untuk export Excel

class HistoryPenjualanController extends Controller // Controller untuk menangani riwayat penjualan
{
    // Menampilkan halaman riwayat transaksi penjualan
    public function index()
    {
        // Ambil semua detail penjualan beserta relasi produk dan pelanggan dari penjualan, lalu dikelompokkan berdasarkan ID penjualan
        $detailTransaksi = DetailPenjualan::with(['produk', 'penjualan.pelanggan'])
            ->get()
            ->groupBy('penjualan_id'); // Mengelompokkan data berdasarkan ID penjualan
    
        // Ambil semua data penjualan dengan relasi pelanggan dan kasir
        $transaksi = Penjualan::with(['pelanggan', 'kasir']) // Termasuk data pelanggan dan kasir
            ->select([
                'id',
                'pelanggan_id',
                'kasir_id',
                'total_bayar',
                'status_pembayaran',
                'created_at'
            ])
            ->get()
            ->keyBy('id'); // Mengatur key array berdasarkan ID penjualan agar mudah dicari

            Log::info('Akses halaman riwayat penjualan', [
                'user' => Auth::user()->name ?? 'Guest',
                'access_time' => now()->toDateTimeString()
            ]);
    
        // Tampilkan data ke view riwayat penjualan
        return view('admin.history-penjualan.index', compact('detailTransaksi', 'transaksi'));
    }

    // Mengekspor data transaksi ke file Excel
    public function exportExcel()
    {
        Log::info('Ekspor data transaksi ke Excel dilakukan', [
            'user' => Auth::user()->name ?? 'Guest',
            'export_time' => now()->toDateTimeString()
        ]);
    
        return Excel::download(new TransaksiExport, 'laporan-transaksi.xlsx'); // Mengunduh file Excel dengan data dari TransaksiExport
    }

    // Menampilkan data yang akan diekspor ke PDF
    public function exportPDF()
    {
        // Ambil semua data penjualan lengkap dengan pelanggan, kasir, dan detail transaksi (termasuk produk)
        $penjualan = Penjualan::with(['pelanggan', 'kasir', 'detailTransaksi.produk'])->get();

        Log::info('Ekspor data transaksi ke PDF dilakukan', [
            'user' => Auth::user()->name ?? 'Guest',
            'export_time' => now()->toDateTimeString()
        ]);
    
        // Tampilkan data ke view khusus untuk export PDF (bisa digunakan langsung oleh DomPDF)
        return view('admin.history-penjualan.export-pdf', compact('penjualan'));
    }
}
