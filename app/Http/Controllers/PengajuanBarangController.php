<?php

namespace App\Http\Controllers;

use App\Helpers\LogActivity;
use App\Exports\PengajuanExport;
use Illuminate\Http\Request;
use App\Models\PengajuanBarang;
use App\Models\Pelanggan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PengajuanBarangController extends Controller {

    // Tampilkan daftar pengajuan barang
    public function index() {  
        $pengajuan = PengajuanBarang::with('pelanggan')->get();
        $pelanggans = Pelanggan::all();
        
        LogActivity::add('Akses Halaman', 'pengajuan_barangs', null, null, 'User mengakses halaman daftar pengajuan barang.');
        
        return view('admin.pengajuan.index', compact('pengajuan', 'pelanggans'));
    }

    // Simpan data pengajuan barang ke database
    public function store(Request $request) {  
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'nama_barang' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
        ]);
    
        $data = $request->all();
        $data['tanggal_pengajuan'] = now();
        $data['status'] = 'tidak terpenuhi';
    
        $pengajuan = PengajuanBarang::create($data);
    
        LogActivity::add('Tambah', 'pengajuan_barangs', $pengajuan->id, null, $pengajuan->toArray());
    
        return redirect()->route('admin.pengajuan.index')
            ->with('success', 'Pengajuan barang berhasil ditambahkan.');
    }

    // Menampilkan data pengajuan barang untuk diedit
    public function edit($id) {  
        $pengajuan = PengajuanBarang::findOrFail($id);

        LogActivity::add('Edit View', 'pengajuan_barangs', $id, null, 'User membuka halaman edit pengajuan.');

        return response()->json($pengajuan);
    }

    // Perbarui data pengajuan barang
    public function update(Request $request, $id) {  
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'nama_barang' => 'required|string|max:255',
            'tanggal_pengajuan' => 'required|date',
            'qty' => 'required|integer|min:1',
        ]);
    
        $pengajuan = PengajuanBarang::findOrFail($id);
        $oldData = $pengajuan->toArray();
        $pengajuan->update($request->except(['id', '_token', '_method'])); // Hindari ID, token, dan method ikut terupdate
    
        LogActivity::add('Update', 'pengajuan_barangs', $id, $oldData, $pengajuan->toArray());
    
        return redirect()->route('admin.pengajuan.index')
            ->with('success', 'Pengajuan barang berhasil diperbarui.');
    }
    

    // Perbarui status pengajuan barang
    public function updateStatus(Request $request, $id) {  
        $request->validate([
            'status' => 'required|in:terpenuhi,tidak terpenuhi',
        ]);

        $pengajuan = PengajuanBarang::findOrFail($id);
        $oldStatus = $pengajuan->status;
        $pengajuan->update(['status' => $request->status]);

        LogActivity::add('Update Status', 'pengajuan_barangs', $id, ['status' => $oldStatus], ['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status pengajuan berhasil diperbarui.'
        ]);
    }

    // Hapus pengajuan barang dari database
    public function destroy($id) {  
        $pengajuan = PengajuanBarang::findOrFail($id);
        $oldData = $pengajuan->toArray();
        $pengajuan->delete();

        LogActivity::add('Hapus', 'pengajuan_barangs', $id, $oldData, null);

        return redirect()->route('admin.pengajuan.index')
            ->with('success', 'Pengajuan barang berhasil dihapus.');
    }

    // Ekspor data pengajuan barang ke file Excel
    public function exportExcel() {  
        LogActivity::add('Ekspor', 'pengajuan_barangs', null, null, 'User mengekspor daftar pengajuan barang ke Excel.');

        return Excel::download(new PengajuanExport, 'pengajuan_barang.xlsx');
    }

    // Ekspor data pengajuan barang ke file PDF
    public function exportPDF() {  
        $pengajuan = PengajuanBarang::all();

        LogActivity::add('Ekspor', 'pengajuan_barangs', null, null, 'User mengekspor daftar pengajuan barang ke PDF.');

        return view('admin.pengajuan.pdf', compact('pengajuan'));
    }
}
