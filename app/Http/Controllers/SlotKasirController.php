<?php

namespace App\Http\Controllers;

// Import model Kasir dan SlotKasir
use App\Models\Kasir;
use App\Models\SlotKasir;
use Illuminate\Http\Request;

class SlotKasirController extends Controller
{
    // Method untuk menampilkan semua data kasir
    public function index()
    {
        $kasirs = Kasir::all(); // Ambil semua data dari tabel kasirs
        return view('admin.slot_kasir.index', compact('kasirs')); // Kirim data ke view slot_kasir.index
    }

    // Method untuk mengupdate data kasir berdasarkan ID
    public function update(Request $request, $id)
    {
        $kasir = Kasir::findOrFail($id); // Cari data kasir berdasarkan ID, jika tidak ditemukan akan throw error 404
        $kasir->nama_kasir = $request->nama_kasir; // Update nama kasir dari input form
        $kasir->slot_kasir = $request->slot_kasir; // Update slot kasir dari input form
        $kasir->save(); // Simpan perubahan ke database
    
        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('slot_kasir.index')->with('success', 'Data kasir berhasil diupdate.');
    }

    // Method untuk menghapus data kasir berdasarkan ID
    public function destroy($id)
    {
        $kasir = Kasir::findOrFail($id);
    
        if ($kasir->penjualan()->count() > 0) {
            return redirect()->back()->with('error', 'Kasir tidak bisa dihapus karena masih memiliki data transaksi.');
        }
    
        $kasir->delete();
        return redirect()->back()->with('success', 'Kasir berhasil dihapus.');
    }
    
}
