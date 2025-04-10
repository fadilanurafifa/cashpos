<?php

namespace App\Http\Controllers;

use App\Helpers\LogActivity; // Helper untuk mencatat log aktivitas
use App\Exports\PengajuanExport; // Export Excel untuk data pengajuan
use Illuminate\Http\Request;
use App\Models\PengajuanBarang; // Model untuk tabel pengajuan barang
use App\Models\Pelanggan; // Model pelanggan
use Barryvdh\DomPDF\Facade\Pdf; // Facade PDF
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PengajuanBarangController extends Controller {

    // ✅ Menampilkan halaman daftar semua pengajuan barang
    public function index() {  
        $pengajuan = PengajuanBarang::with('pelanggan')->get(); // Ambil semua data pengajuan dan relasi pelanggan
        $pelanggans = Pelanggan::all(); // Ambil data semua pelanggan

        // Catat log akses halaman
        LogActivity::add('Akses Halaman', 'pengajuan_barangs', null, null, 'User mengakses halaman daftar pengajuan barang.');
        
        return view('admin.pengajuan.index', compact('pengajuan', 'pelanggans')); // Tampilkan ke view
    }

    // ✅ Menyimpan data pengajuan baru ke database
    public function store(Request $request) {  
        // Validasi input form
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id', // Pastikan pelanggan valid
            'nama_barang' => 'required|string|max:255',       // Nama barang wajib
            'qty' => 'required|integer|min:1',                // Minimal quantity = 1
        ]);
    
        $data = $request->all(); // Ambil semua input dari form
        $data['tanggal_pengajuan'] = now(); // Isi tanggal saat ini
        $data['status'] = 'tidak terpenuhi'; // Status default
    
        $pengajuan = PengajuanBarang::create($data); // Simpan ke database

        // Log aktivitas penambahan pengajuan
        LogActivity::add('Tambah', 'pengajuan_barangs', $pengajuan->id, null, $pengajuan->toArray());
    
        return redirect()->route('admin.pengajuan.index')
            ->with('success', 'Pengajuan barang berhasil ditambahkan.');
    }

    // ✅ Menampilkan data pengajuan tertentu untuk diedit (via AJAX)
    public function edit($id) {  
        $pengajuan = PengajuanBarang::findOrFail($id); // Cari pengajuan berdasarkan ID

        // Catat log bahwa user membuka halaman edit
        LogActivity::add('Edit View', 'pengajuan_barangs', $id, null, 'User membuka halaman edit pengajuan.');

        return response()->json($pengajuan); // Kirim data JSON untuk form edit
    }

    // ✅ Menyimpan perubahan data pengajuan ke database
    public function update(Request $request, $id) {  
        // Validasi input update
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'nama_barang' => 'required|string|max:255',
            'tanggal_pengajuan' => 'required|date',
            'qty' => 'required|integer|min:1',
        ]);
    
        $pengajuan = PengajuanBarang::findOrFail($id); // Cari data
        $oldData = $pengajuan->toArray(); // Simpan data lama
        $pengajuan->update($request->except(['id', '_token', '_method'])); // Update data kecuali ID dan token
    
        // Catat log update
        LogActivity::add('Update', 'pengajuan_barangs', $id, $oldData, $pengajuan->toArray());
    
        return redirect()->route('admin.pengajuan.index')
            ->with('success', 'Pengajuan barang berhasil diperbarui.');
    }

    // ✅ Update status dari pengajuan (terpenuhi / tidak terpenuhi)
    public function updateStatus(Request $request, $id) {  
        // Validasi status hanya boleh dua pilihan
        $request->validate([
            'status' => 'required|in:terpenuhi,tidak terpenuhi',
        ]);

        $pengajuan = PengajuanBarang::findOrFail($id);
        $oldStatus = $pengajuan->status;
        $pengajuan->update(['status' => $request->status]); // Simpan status baru

        // Catat log perubahan status
        LogActivity::add('Update Status', 'pengajuan_barangs', $id, ['status' => $oldStatus], ['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status pengajuan berhasil diperbarui.'
        ]);
    }

    // ✅ Menghapus pengajuan barang dari database
    public function destroy($id) {  
        $pengajuan = PengajuanBarang::findOrFail($id);
        $oldData = $pengajuan->toArray();
        $pengajuan->delete(); // Hapus dari database

        // Log aktivitas penghapusan
        LogActivity::add('Hapus', 'pengajuan_barangs', $id, $oldData, null);

        return redirect()->route('admin.pengajuan.index')
            ->with('success', 'Pengajuan barang berhasil dihapus.');
    }

    // ✅ Export semua data pengajuan ke file Excel
    public function exportExcel() {  
        LogActivity::add('Ekspor', 'pengajuan_barangs', null, null, 'User mengekspor daftar pengajuan barang ke Excel.');

        return Excel::download(new PengajuanExport, 'pengajuan_barang.xlsx'); // Unduh file Excel
    }

    // ✅ Export semua data pengajuan ke file PDF
    public function exportPDF() {  
        $pengajuan = PengajuanBarang::all(); // Ambil semua data

        LogActivity::add('Ekspor', 'pengajuan_barangs', null, null, 'User mengekspor daftar pengajuan barang ke PDF.');

        return view('admin.pengajuan.pdf', compact('pengajuan')); // Tampilkan view PDF
    }
}
