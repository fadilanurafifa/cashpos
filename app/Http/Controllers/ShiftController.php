<?php

namespace App\Http\Controllers;

use App\Models\Kasir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShiftController extends Controller
{
    // Menampilkan halaman shift kasir
    public function index()
    {
        $daftarKasir = Kasir::all(); // Mengambil semua data kasir

        // Logging akses halaman shift kasir
        Log::info('Akses halaman daftar kasir shift.');

        return view('kasir.shift', compact('daftarKasir')); // Menampilkan view dengan daftar kasir
    }
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'slot' => 'required', // Slot kasir wajib diisi
            'kasir_id' => 'nullable', // kasir_id boleh kosong (jika user memilih 'lainnya')
            'nama_baru' => 'required_if:kasir_id,lainnya' // Jika kasir_id adalah 'lainnya', maka nama_baru wajib diisi
        ]);
    
        $kasirId = null; // Inisialisasi variabel untuk menyimpan ID kasir
    
        // Logging permintaan simpan shift kasir
        Log::info('Request store kasir shift diterima.', [
            'slot' => $request->slot,
            'kasir_id' => $request->kasir_id,
            'nama_baru' => $request->nama_baru,
        ]);
        
        // Jika user memilih untuk menambahkan kasir baru
        if ($request->kasir_id === 'lainnya') {
            // Buat kasir baru dengan nama dan slot dari input
            $kasirBaru = Kasir::create([
                'nama_kasir' => $request->nama_baru, // Simpan nama kasir baru
                'slot_kasir' => $request->slot, // Simpan slot kasir baru
            ]);
            $kasirId = $kasirBaru->id; // Simpan ID kasir baru
            Log::info("Kasir baru dibuat dengan ID: $kasirId"); // Catat ke log
        } else {
            // Jika memilih kasir yang sudah ada
            $kasir = Kasir::find($request->kasir_id); // Cari data kasir berdasarkan ID
    
            if (!$kasir) {
                // Jika kasir tidak ditemukan di database
                Log::error("Kasir ID {$request->kasir_id} tidak ditemukan."); // Log error
                return back()->withErrors(['kasir_id' => 'Kasir tidak ditemukan.']); // Kembalikan ke form dengan pesan error
            }
    
            // Update slot kasir di database agar sesuai slot yang dipilih saat ini
            $kasir->slot_kasir = $request->slot; // Update slot kasir
            $kasir->save(); // Simpan perubahan ke database
    
            $kasirId = $kasir->id; // Simpan ID kasir lama
            Log::info("Kasir lama dipilih, ID: $kasirId, slot diupdate ke: {$request->slot}"); // Catat ke log
        }
    
        // Simpan ID kasir ke dalam session agar bisa digunakan di halaman berikutnya
        session([
            'kasir_id' => $kasirId, // Simpan ID kasir ke session
        ]);
        Log::info("Session kasir_id diset: " . session('kasir_id')); // Catat ke log
    
        // Redirect ke halaman penjualan dengan pesan sukses
        return redirect()->route('penjualan.index')->with('success', 'Shift dimulai'); // Arahkan user ke halaman penjualan
    }
    

    // Menyimpan data shift kasir (kasir baru atau kasir lama)
    // public function store(Request $request)
    // {
    //     // Validasi input dari form
    //     $request->validate([
    //         'slot' => 'required', // slot harus diisi
    //         'kasir_id' => 'nullable', // kasir_id bisa null
    //         'nama_baru' => 'required_if:kasir_id,lainnya' // jika kasir_id = lainnya, maka nama_baru wajib diisi
    //     ]);

    //     $kasirId = null; // Inisialisasi variabel kasirId

    //     // Jika user memilih kasir baru (opsi "lainnya")
    //     if ($request->kasir_id === 'lainnya') {
    //         // Buat kasir baru di database
    //         $kasirBaru = Kasir::create([
    //             'nama_kasir' => $request->nama_baru, // nama kasir baru
    //             'slot_kasir' => $request->slot, // slot shift
    //         ]);
    //         $kasirId = $kasirBaru->id; // Simpan ID kasir baru ke variabel
    //         Log::info("Kasir baru dibuat dengan ID: $kasirId"); // Catat ke log
    //     } else {
    //         // Jika memilih kasir lama
    //         $kasir = Kasir::find($request->kasir_id); // Cari kasir berdasarkan ID

    //         // Jika kasir tidak ditemukan
    //         if (!$kasir) {
    //             Log::error("Kasir ID {$request->kasir_id} tidak ditemukan."); // Catat error
    //             return back()->withErrors(['kasir_id' => 'Kasir tidak ditemukan.']); // Kembali ke halaman sebelumnya dengan pesan error
    //         }

    //         $kasirId = $kasir->id; // Simpan ID kasir lama
    //         Log::info("Kasir lama dipilih, ID: $kasirId"); // Catat ke log
    //     }

    //     // Simpan ID kasir ke dalam session
    //     session([
    //         'kasir_id' => $kasirId,
    //     ]);
    //     Log::info("Session kasir_id diset: " . session('kasir_id')); // Catat ke log

    //     // Redirect ke halaman penjualan dengan pesan sukses
    //     return redirect()->route('penjualan.index')->with('success', 'Shift dimulai');
    // }
}
