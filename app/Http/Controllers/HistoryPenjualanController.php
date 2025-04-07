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
        $detailTransaksi = DetailPenjualan::with(['produk', 'penjualan.pelanggan'])
            ->get()
            ->groupBy('penjualan_id');
    
        $transaksi = Penjualan::with(['pelanggan', 'kasir']) // include kasir relation
            ->select([
                'id',
                'pelanggan_id',
                'kasir_id',
                'total_bayar',
                'status_pembayaran',
                'created_at'
            ])
            ->get()
            ->keyBy('id');
    
        return view('admin.history-penjualan.index', compact('detailTransaksi', 'transaksi'));
    }
        // Export ke Excel
    public function exportExcel()
    {
        return Excel::download(new TransaksiExport, 'laporan-transaksi.xlsx');
    }

    // Export ke PDF
    public function exportPDF()
    {
        $penjualan = Penjualan::with(['pelanggan', 'kasir', 'detailTransaksi.produk'])->get();
    
        return view('admin.history-penjualan.export-pdf', compact('penjualan'));
    }
    
    
}
