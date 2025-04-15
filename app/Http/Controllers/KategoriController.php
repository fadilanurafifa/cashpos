<?php
namespace App\Http\Controllers; // Namespace controller utama

use App\Exports\KategoriExport; // Import class export untuk Excel
use App\Imports\KategoriImport;
use App\Models\Kategori; // Import model Kategori
use Barryvdh\DomPDF\Facade\Pdf; // Import PDF facade
use Illuminate\Http\Request; // Import Request untuk menangani input/form
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel; // Import Excel facade untuk export file
use Illuminate\Support\Facades\Auth; // Import Auth untuk mengakses user yang login

class KategoriController extends Controller
{
    //  Menampilkan daftar semua kategori
    public function index()
    {
        $kategori = Kategori::all(); // Ambil semua data kategori dari tabel

        Log::info('Akses halaman daftar kategori', [
            'user' => Auth::user()->name ?? 'Guest',
            'access_time' => now()->toDateTimeString()
        ]);
    
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

            Log::info('Kategori baru ditambahkan', [
                'user' => Auth::user()->name ?? 'Guest',
                'kategori' => $request->nama_kategori,
                'created_at' => now()->toDateTimeString()
            ]);    

            // Redirect kembali ke halaman kategori dengan notifikasi sukses
            return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan!');
        } catch (\Exception $e) {

            Log::error('Gagal menambahkan kategori', [
                'error' => $e->getMessage(),
                'user' => Auth::user()->name ?? 'Guest',
                'kategori' => $request->nama_kategori
            ]);

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
            $oldName = $kategori->nama_kategori; // Simpan nama kategori sebelumnya
            $kategori->update([
                'nama_kategori' => $request->nama_kategori, // Update nama kategori
            ]);

            Log::info('Kategori diperbarui', [
                'user' => Auth::user()->name ?? 'Guest',
                'kategori_id' => $id,
                'old_name' => $oldName,
                'new_name' => $request->nama_kategori,
                'updated_at' => now()->toDateTimeString()
            ]);

            // Kirim respons sukses dalam bentuk JSON
            return response()->json([
                'status' => 'success',
                'message' => 'Kategori berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {

            Log::error('Gagal memperbarui kategori', [
                'error' => $e->getMessage(),
                'user' => Auth::user()->name ?? 'Guest',
                'kategori_id' => $id
            ]);
    
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

            Log::info('Kategori dihapus', [
                'user' => Auth::user()->name ?? 'Guest',
                'kategori_id' => $id,
                'deleted_at' => now()->toDateTimeString()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Kategori berhasil dihapus!' // Kirim JSON sukses
            ]);
        } catch (\Exception $e) {

             Log::error('Gagal menghapus kategori', [
            'user' => Auth::user()->name ?? 'Guest',
            'kategori_id' => $id,
            'error' => $e->getMessage()
        ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus kategori!' // Kirim JSON error
            ], 500);
        }
    }

    //  Export kategori ke file Excel
    public function exportExcel()
    {
        Log::info('Export data kategori ke Excel', [
            'user' => Auth::user()->name ?? 'Guest',
            'time' => now()->toDateTimeString()
        ]);

        return Excel::download(new KategoriExport, 'laporan_kategori.xlsx'); // Download file Excel
    }

    //  Export kategori ke PDF (saat ini hanya menampilkan view, belum generate PDF langsung)
    public function exportPDF()
    {
        $kategori = Kategori::all(); // Ambil semua data kategori

        Log::info('Export data kategori ke PDF (view)', [
            'user' => Auth::user()->name ?? 'Guest',
            'jumlah_kategori' => $kategori->count(),
            'time' => now()->toDateTimeString()
        ]);
    
        return view('admin.kategori.pdf', compact('kategori')); // Tampilkan view PDF (bisa dikonversi oleh DomPDF)
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);

        Excel::import(new KategoriImport, $request->file('file'));

        return redirect()->back()->with('success', 'Data pelanggan berhasil diimport!');
    }

}
