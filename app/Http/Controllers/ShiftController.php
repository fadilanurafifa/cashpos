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
        return view('kasir.shift', compact('daftarKasir')); // Menampilkan view dengan daftar kasir
    }

    // Menyimpan data shift kasir (kasir baru atau kasir lama)
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'slot' => 'required', // slot harus diisi
            'kasir_id' => 'nullable', // kasir_id bisa null
            'nama_baru' => 'required_if:kasir_id,lainnya' // jika kasir_id = lainnya, maka nama_baru wajib diisi
        ]);

        $kasirId = null; // Inisialisasi variabel kasirId

        // Jika user memilih kasir baru (opsi "lainnya")
        if ($request->kasir_id === 'lainnya') {
            // Buat kasir baru di database
            $kasirBaru = Kasir::create([
                'nama_kasir' => $request->nama_baru, // nama kasir baru
                'slot_kasir' => $request->slot, // slot shift
            ]);
            $kasirId = $kasirBaru->id; // Simpan ID kasir baru ke variabel
            Log::info("Kasir baru dibuat dengan ID: $kasirId"); // Catat ke log
        } else {
            // Jika memilih kasir lama
            $kasir = Kasir::find($request->kasir_id); // Cari kasir berdasarkan ID

            // Jika kasir tidak ditemukan
            if (!$kasir) {
                Log::error("Kasir ID {$request->kasir_id} tidak ditemukan."); // Catat error
                return back()->withErrors(['kasir_id' => 'Kasir tidak ditemukan.']); // Kembali ke halaman sebelumnya dengan pesan error
            }

            $kasirId = $kasir->id; // Simpan ID kasir lama
            Log::info("Kasir lama dipilih, ID: $kasirId"); // Catat ke log
        }

        // Simpan ID kasir ke dalam session
        session([
            'kasir_id' => $kasirId,
        ]);
        Log::info("Session kasir_id diset: " . session('kasir_id')); // Catat ke log

        // Redirect ke halaman penjualan dengan pesan sukses
        return redirect()->route('penjualan.index')->with('success', 'Shift dimulai');
    }
}
