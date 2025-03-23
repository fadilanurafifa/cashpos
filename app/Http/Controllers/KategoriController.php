<?php
namespace App\Http\Controllers;

use App\Exports\KategoriExport;
use App\Models\Kategori;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class KategoriController extends Controller
{
    // Menampilkan daftar kategori
    public function index()
    {
        $kategori = Kategori::all(); // Mengambil semua data kategori dari database
        return view('admin.kategori.index', compact('kategori')); // Mengirimkan data kategori ke view
    }

    // Menyimpan kategori ke database
    public function store(Request $request)
    {
        // Validasi input form
        $request->validate([
            'nama_kategori' => 'required|unique:kategori|max:100', // Nama kategori harus unik dan maksimal 100 karakter
        ]);

        try {
            // Menyimpan kategori baru ke database
            Kategori::create([
                'nama_kategori' => $request->nama_kategori,
            ]);

            return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan!'); // Redirect dengan pesan sukses
        } catch (\Exception $e) {
            return redirect()->route('kategori.index')->with('error', 'Gagal menambahkan kategori!'); // Redirect dengan pesan error
        }
    }   

    // Menampilkan form edit kategori
        public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|max:100|unique:kategori,nama_kategori,' . $id,
        ]);

        try {
            $kategori = Kategori::findOrFail($id);
            $kategori->update([
                'nama_kategori' => $request->nama_kategori,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Kategori berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui kategori!'
            ], 500);
        }
    }


    // Menghapus kategori dari database
    public function destroy($id)
    {
        try {
            $kategori = Kategori::findOrFail($id); // Cari kategori berdasarkan ID, atau gagal jika tidak ditemukan
            $kategori->delete(); // Hapus kategori dari database
    
            return response()->json([
                'status' => 'success',
                'message' => 'Kategori berhasil dihapus!' // Kirim respons sukses dalam format JSON
            ]);
        } catch (\Exception $e) {
            \log("error", "Gagal menghapus kategori: " . $e->getMessage()); // Catat error di log jika gagal
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus kategori!' // Kirim respons error dalam format JSON
            ], 500);
        }
    }   

        public function exportExcel()
    {
        return Excel::download(new KategoriExport, 'laporan_kategori.xlsx');
    }
    public function exportPDF()
    {
        $kategori = Kategori::all();
        return view('admin.kategori.pdf', compact('kategori')); // Menampilkan view, bukan PDF
    }
    
}
