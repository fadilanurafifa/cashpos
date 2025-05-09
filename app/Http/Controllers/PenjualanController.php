<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Order;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\User;
use App\Notifications\OrderForChefNotification;
use App\Notifications\PesananMasukChefNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PenjualanController extends Controller
{
    // Menampilkan daftar penjualan dengan informasi pelanggan dan produk
    public function index()
    {
        Log::info('Mengakses halaman daftar penjualan', [
            'user' => Auth::user()->name ?? 'Guest',
            'timestamp' => now()->toDateTimeString()
        ]);
    
        $pelanggan = Pelanggan::all(); // Ambil semua data pelanggan
        $penjualan = Penjualan::with('pelanggan')->paginate(10); // Ambil daftar penjualan dengan relasi pelanggan
        $produk = Produk::select('id', 'nama_produk', 'harga', 'foto')->get(); // Ambil daftar produk yang tersedia

        return view('admin.penjualan.index', compact('pelanggan', 'penjualan', 'produk')); // Kirim data ke tampilan
    }

    // Menyimpan transaksi penjualan baru
    public function store(Request $request)
    {
        Log::info('Permintaan untuk menyimpan penjualan diterima', [
            'user' => Auth::user()->name ?? 'Guest',
            'data' => $request->all(),
            'timestamp' => now()->toDateTimeString()
        ]);

        // Validasi input
        $validated = $request->validate([
            'pelanggan_id' => 'required', // Pastikan pelanggan dipilih
            'produk' => 'required|array|min:1', // Produk harus ada minimal 1 item
            'produk.*.produk_id' => 'required', // Pastikan setiap produk memiliki ID
            'produk.*.jumlah' => 'required|integer|min:1', // Pastikan jumlah produk minimal 1
        ]);
    
        Log::info('Request Penjualan:', $request->all()); // Log data input untuk debugging
    
        DB::beginTransaction(); // Mulai transaksi database
        try {
            $totalBayar = 0; // Variabel untuk menyimpan total harga transaksi
    
            // Cek apakah ada transaksi sebelumnya untuk menentukan nomor faktur baru
            $lastPenjualan = Penjualan::latest()->first();
            if ($lastPenjualan && preg_match('/\d+/', $lastPenjualan->no_faktur, $matches)) {
                $lastNumber = (int) $matches[0];
                $no_faktur = 'FTR-' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT); // Buat nomor faktur baru dengan format FTR-XXXXX
            } else {
                $no_faktur = 'FTR-00001'; // Jika belum ada transaksi sebelumnya, gunakan nomor faktur pertama
            }
    
            // Validasi apakah produk yang dipilih ada di database
            $produkIds = array_column($validated['produk'], 'produk_id');
            $produkTersedia = Produk::whereIn('id', $produkIds)->pluck('id')->toArray();
            foreach ($produkIds as $id) {
                if (!in_array($id, $produkTersedia)) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Produk dengan ID ' . $id . ' tidak ditemukan.',
                    ], 400);
                }
            }
    
            // Simpan transaksi penjualan
            // $penjualan = Penjualan::create([
            //     'no_faktur' => $no_faktur,
            //     'tgl_faktur' => now(),
            //     'total_bayar' => 0, // Akan diperbarui setelah semua item dihitung
            //     'pelanggan_id' => $validated['pelanggan_id'],
            //     'user_id' => 1, // ID kasir, bisa diganti dengan `Auth::id()` jika user login digunakan
            //     'metode_pembayar' => $request->metode_pembayaran ?? 'cash', // Default ke 'cash' jika metode tidak dipilih
                
            // ]);

            logger('Session saat transaksi:', [
                'kasir_slot' => session('kasir_slot'),
                'kasir_nama' => session('kasir_nama'),
            ]);
            
            $penjualan = Penjualan::create([
                'no_faktur' => $no_faktur,
                'tgl_faktur' => now(),
                'total_bayar' => 0,
                'pelanggan_id' => $validated['pelanggan_id'],
                'user_id' => Auth::id(), // Simpan user login
                'kasir_id' => session('kasir_id'), // ⬅️ Tambahkan ini!
                'metode_pembayar' => $request->metode_pembayaran ?? 'cash',
            ]);
            
    
            // Simpan detail penjualan (produk yang dibeli)
            foreach ($validated['produk'] as $item) {
                $produk = Produk::find($item['produk_id']);
                $hargaJual = $produk->harga; // Ambil harga produk
                $subTotal = $hargaJual * $item['jumlah']; // Hitung subtotal untuk produk tersebut
                $totalBayar += $subTotal; // Tambahkan ke total bayar
    
                DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'produk_id' => $item['produk_id'],
                    'jumlah' => $item['jumlah'],
                    'sub_total' => $subTotal, // Simpan harga subtotal produk
                ]);
            }
    
            // Update total bayar setelah semua item dihitung
            $penjualan->update(['total_bayar' => $totalBayar]);
    
            DB::commit(); // Simpan transaksi ke database

            Log::info('Transaksi penjualan berhasil disimpan', [
                'no_faktur' => $no_faktur,
                'total' => $totalBayar,
                'timestamp' => now()->toDateTimeString()
            ]);
    
            return response()->json([
                'success' => true,
                'no_faktur' => $penjualan->no_faktur, // Kirim nomor faktur sebagai respon
                'total_bayar' => $penjualan->total_bayar, // Kirim total bayar sebagai respon
                'kasir' => $penjualan->kasir->nama_kasir ?? '-',
            ]);
        } catch (\Exception $e) {
            DB::rollback(); // Batalkan transaksi jika terjadi kesalahan
            // logging error
            Log::error('Gagal menyimpan transaksi penjualan', [
                'error' => $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Gagal menyimpan transaksi. ' . $e->getMessage(),
            ], 400);
        }
    }  

    // Menampilkan daftar notifikasi untuk kasir yang login
    public function notifications()
    {
        $user = Auth::user();
        
        Log::info('User membuka halaman notifikasi', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'timestamp' => now()->toDateTimeString()
        ]);
        $notifications = Auth::user()->notifications; // Ambil notifikasi dari user yang sedang login
        return view('kasir.notifications', compact('notifications')); // Kirim data ke tampilan notifikasi
    }    
}

