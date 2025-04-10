<?php
namespace App\Http\Controllers; // Namespace controller utama

use App\Exports\KategoriExport; // Import class export untuk Excel
use App\Models\Kategori; // Import model Kategori
use Barryvdh\DomPDF\Facade\Pdf; // Import PDF facade
use Illuminate\Http\Request; // Import Request untuk menangani input/form
use Maatwebsite\Excel\Facades\Excel; // Import Excel facade untuk export file

class KategoriController extends Controller
{
    //  Menampilkan daftar semua kategori
    public function index()
    {
        $kategori = Kategori::all(); // Ambil semua data kategori dari tabel
        return view('admin.kategori.index', compact('kategori')); // Kirim data ke view index kategori
    }

    //  Menyimpan kategori baru ke database
    public function store(Request $request)
    {
        // Validasi input form kategori
        $request->validate([
            'nama_kategori' => 'required|unique:kategori|max:100', // Wajib, unik di tabel kategori, max 100 karakter
        ]);

        try {
            // Simpan data ke database
            Kategori::create([
                'nama_kategori' => $request->nama_kategori,
            ]);

            // Redirect kembali ke halaman kategori dengan notifikasi sukses
            return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan!');
        } catch (\Exception $e) {
            // Jika error, redirect dengan notifikasi gagal
            return redirect()->route('kategori.index')->with('error', 'Gagal menambahkan kategori!');
        }
    }

    //  Memperbarui data kategori yang sudah ada
    public function update(Request $request, $id)
    {
        // Validasi input (dengan pengecualian ID sendiri agar tidak dianggap duplikat)
        $request->validate([
            'nama_kategori' => 'required|max:100|unique:kategori,nama_kategori,' . $id,
        ]);

        try {
            $kategori = Kategori::findOrFail($id); // Cari data kategori berdasarkan ID
            $kategori->update([
                'nama_kategori' => $request->nama_kategori, // Update nama kategori
            ]);

            // Kirim respons sukses dalam bentuk JSON
            return response()->json([
                'status' => 'success',
                'message' => 'Kategori berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            // Kirim respons error jika gagal
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui kategori!'
            ], 500);
        }
    }

    //  Menghapus data kategori
    public function destroy($id)
    {
        try {
            $kategori = Kategori::findOrFail($id); // Cari kategori berdasarkan ID
            $kategori->delete(); // Hapus dari database

            return response()->json([
                'status' => 'success',
                'message' => 'Kategori berhasil dihapus!' // Kirim JSON sukses
            ]);
        } catch (\Exception $e) {
            \log("error", "Gagal menghapus kategori: " . $e->getMessage()); // Catat ke log jika error
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus kategori!' // Kirim JSON error
            ], 500);
        }
    }

    //  Export kategori ke file Excel
    public function exportExcel()
    {
        return Excel::download(new KategoriExport, 'laporan_kategori.xlsx'); // Download file Excel
    }

    //  Export kategori ke PDF (saat ini hanya menampilkan view, belum generate PDF langsung)
    public function exportPDF()
    {
        $kategori = Kategori::all(); // Ambil semua data kategori
        return view('admin.kategori.pdf', compact('kategori')); // Tampilkan view PDF (bisa dikonversi oleh DomPDF)
    }
}
