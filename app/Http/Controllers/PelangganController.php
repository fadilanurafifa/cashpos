<?php

namespace App\Http\Controllers; // Deklarasi namespace controller Laravel

use App\Exports\PelangganExport; // Import class export Excel untuk pelanggan
use Illuminate\Http\Request; // Import class Request untuk menangani input
use App\Models\Pelanggan; // Import model Pelanggan
use Barryvdh\DomPDF\Facade\Pdf; // Import facade DomPDF
use Maatwebsite\Excel\Facades\Excel; // Import facade Excel

class PelangganController extends Controller
{
    //  Menampilkan daftar semua pelanggan
    public function index()
    {
        $pelanggan = Pelanggan::orderBy('created_at', 'desc')->get(); // Ambil semua pelanggan, urutkan dari yang terbaru
        return view('admin.pelanggan.index', compact('pelanggan')); // Kirim data ke view pelanggan
    }

    //  Menyimpan pelanggan baru ke database
    public function store(Request $request)
    {
        // Buat kode pelanggan unik dengan format "P-00001"
        $kode_pelanggan = 'P-' . str_pad(Pelanggan::count() + 1, 5, '0', STR_PAD_LEFT);

        // Validasi input form
        $request->validate([
            'nama' => 'required|string|max:255', // Nama wajib, maksimal 255 karakter
            'alamat' => 'required|string',       // Alamat wajib
            'no_telp' => 'nullable|string',      // Nomor telepon opsional
            'email' => 'nullable|email',         // Email opsional, harus format email jika diisi
        ]);

        // Simpan data pelanggan ke database
        Pelanggan::create([
            'kode_pelanggan' => $kode_pelanggan,
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'no_telp' => $request->no_telp,
            'email' => $request->email,
        ]);

        return redirect()->back()->with('success', 'Pelanggan berhasil ditambahkan'); // Redirect dengan notifikasi
    }

    //  Mengambil data pelanggan tertentu untuk diedit
    public function edit($id)
    {
        $pelanggan = Pelanggan::findOrFail($id); // Cari pelanggan berdasarkan ID, gagal jika tidak ada
        return response()->json($pelanggan); // Kembalikan data pelanggan dalam format JSON
    }

    //  Memperbarui data pelanggan
    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id); // Cari pelanggan

        $pelanggan->update($request->all()); // Perbarui semua field berdasarkan input request

        return response()->json(['success' => true]); // Kembalikan respons sukses (bisa untuk notifikasi JS)
    }

    //  Menghapus pelanggan berdasarkan ID
    public function destroy($id)
    {
        Pelanggan::destroy($id); // Hapus data pelanggan langsung berdasarkan ID

        return response()->json(['success' => true]); // Kirim respons sukses
    }

    //  Export data pelanggan ke Excel
    public function exportExcel()
    {
        return Excel::download(new PelangganExport, 'pelanggan.xlsx'); // Unduh file Excel pelanggan
    }
    
    //  Export data pelanggan ke PDF (saat ini hanya menampilkan view PDF)
    public function exportPdf()
    {
        $pelanggan = Pelanggan::all(); // Ambil semua pelanggan
        return view('admin.pelanggan.pdf', compact('pelanggan')); // Tampilkan view PDF (belum mengunduh langsung)
    }
}
