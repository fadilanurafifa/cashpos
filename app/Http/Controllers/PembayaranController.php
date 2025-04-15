<?php

namespace App\Http\Controllers;

use App\Events\PesananBaruEvent;
use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Order;
use App\Models\User;
use App\Notifications\PesananBaruNotification;
use App\Notifications\PesananKeChefNotification;
use App\Services\ServiceThermal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PembayaranController extends Controller
{
    // Menampilkan detail transaksi berdasarkan no_faktur
    public function show($no_faktur) 
    {
        $transaksi = Penjualan::where('no_faktur', $no_faktur)->first(); // Ambil transaksi berdasarkan nomor faktur

        Log::info('Menampilkan detail transaksi', [
            'user' => Auth::user()->name ?? 'Guest',
            'no_faktur' => $no_faktur,
            'time' => now()->toDateTimeString()
        ]);

        if (!$transaksi) {
            return redirect()->route('admin.pembayaran.index')->with('error', 'Transaksi tidak ditemukan.'); // Redirect jika transaksi tidak ditemukan
        }

        $detail_penjualan = DetailPenjualan::with('produk')->where('penjualan_id', $transaksi->id)->get(); // Ambil detail transaksi dan produk terkait

        return view('admin.pembayaran.show', compact('transaksi', 'detail_penjualan')); // Kirim data ke tampilan pembayaran
    }

    // Memproses pembayaran transaksi
    public function bayar(Request $request, $no_faktur)
    {
        $transaksi = Penjualan::where('no_faktur', $no_faktur)->with('detail_penjualan')->first(); // Cari transaksi berdasarkan nomor faktur

        Log::info('Memproses pembayaran transaksi', [
            'user' => Auth::user()->name ?? 'Guest',
            'no_faktur' => $no_faktur,
            'jumlah_bayar' => $request->jumlah_bayar,
            'time' => now()->toDateTimeString()
        ]);
        
        if (!$transaksi) {
            Log::warning('Transaksi tidak ditemukan', [
                'no_faktur' => $no_faktur,
                'time' => now()->toDateTimeString()
            ]);
            return redirect()->route('admin.pembayaran.index')->with('error', 'Transaksi tidak ditemukan.'); // Redirect jika transaksi tidak ditemukan
        }

        // Validasi jumlah bayar, harus lebih dari atau sama dengan total bayar
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:' . $transaksi->total_bayar,
        ]);

        $jumlah_bayar = $request->jumlah_bayar; // Ambil jumlah bayar dari input
        $kembalian = $jumlah_bayar - $transaksi->total_bayar; // Hitung kembalian jika ada

        // Jika jumlah bayar kurang dari total, kembalikan error
        if ($jumlah_bayar < $transaksi->total_bayar) {
            return redirect()->back()->with('error', 'Jumlah bayar kurang dari total yang harus dibayar.');
        }

        // Update status transaksi menjadi "lunas"
        $transaksi->update([
            'status_pembayaran' => 'lunas',
        ]);

         // Log pembayaran berhasil
        Log::info('Pembayaran berhasil', [
            'no_faktur' => $no_faktur,
            'status_pembayaran' => 'lunas',
            'jumlah_bayar' => $jumlah_bayar,
            'kembalian' => $kembalian,
            'time' => now()->toDateTimeString()
        ]);

        // Ambil semua detail transaksi
        $detail_penjualan = DetailPenjualan::where('penjualan_id', $transaksi->id)->get();

        // Kurangi stok produk sesuai jumlah yang dibeli
        foreach ($detail_penjualan as $detail) {
            $produk = $detail->produk;
            if ($produk) {
                if ($produk->stok >= $detail->jumlah) {
                    $produk->stok -= $detail->jumlah; // Kurangi stok
                    $produk->save(); // Simpan perubahan
                } else {
                    Log::error('Stok tidak mencukupi', [
                        'produk' => $produk->nama_produk,
                        'stok_tersedia' => $produk->stok,
                        'jumlah_dibeli' => $detail->jumlah,
                        'time' => now()->toDateTimeString()
                    ]);
                    return redirect()->back()->with('error', 'Stok tidak mencukupi untuk produk: ' . $produk->nama_produk); // Jika stok tidak cukup, kembalikan error
                }
            }
        }


        $thermal = new ServiceThermal();
        $thermal->cetakStruk($transaksi);

        return redirect()->route('admin.pembayaran.show', $no_faktur) // Redirect ke halaman pembayaran
            ->with('success', 'Pembayaran berhasil!') 
            ->with('jumlah_bayar', $jumlah_bayar) 
            ->with('kembalian', $kembalian);
    }

    // Mencetak struk pembayaran dalam tampilan HTML
    public function print($no_faktur)
    {
        Log::info('Mencetak struk pembayaran', [
            'user' => Auth::user()->name ?? 'Guest',
            'no_faktur' => $no_faktur,
            'time' => now()->toDateTimeString()
        ]);

        $transaksi = Penjualan::where('no_faktur', $no_faktur)->first(); // Cari transaksi berdasarkan nomor faktur
    
        if (!$transaksi) {

            // Log jika transaksi tidak ditemukan
            Log::warning('Transaksi tidak ditemukan untuk print', [
                'no_faktur' => $no_faktur,
                'time' => now()->toDateTimeString()
            ]);
            return back()->with('error', 'Transaksi tidak ditemukan'); // Redirect jika transaksi tidak ditemukan
        }

        $detail_penjualan = DetailPenjualan::where('penjualan_id', $transaksi->id)->get(); // Ambil detail transaksi
    
        return view('admin.pembayaran.struk', compact('transaksi', 'detail_penjualan')); // Kirim data ke tampilan struk pembayaran
    }
}
