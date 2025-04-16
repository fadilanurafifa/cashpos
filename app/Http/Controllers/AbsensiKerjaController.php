<?php

namespace App\Http\Controllers;

use App\Exports\AbsensiExport;
use App\Imports\AbsensiImport;
use App\Models\AbsenKerja;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class AbsensiKerjaController extends Controller
{
    public function index()
    {
        $absensi = AbsenKerja::with('user')->latest()->get();
        $absensi = AbsenKerja::with('user.kasir')->get();

        return view('admin.absensi.index', compact('absensi'));
    }


        public function store(Request $request)
    {
        // dd($request->all());
        // Validasi input
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status_masuk' => 'required|in:masuk,izin,alfa',
            'waktu_masuk' => 'required|date',
        ]);

        // Pastikan waktu_masuk dikirim dalam format yang benar (Y-m-d H:i:s)
        $waktuMasuk = \Carbon\Carbon::parse($request->waktu_masuk)->format('Y-m-d H:i:s');
        
        // Ambil tanggal dari waktu_masuk untuk disimpan di kolom tanggal_masuk
        $tanggal = \Carbon\Carbon::parse($request->waktu_masuk)->format('Y-m-d H:i:s');
        
        // Tentukan waktu_akhir_kerja jika status_masuk adalah 'sakit' atau 'cuti'
        $waktuAkhirKerja = null;
        if (in_array($request->status_masuk, ['sakit', 'cuti'])) {
            $waktuAkhirKerja = now(); // Waktu saat ini
        }

        // Siapkan data untuk disimpan
        $data = [
            'user_id' => $request->user_id,
            'status_masuk' => $request->status_masuk,
            'waktu_masuk' => $waktuMasuk,
            'tanggal_masuk' => $tanggal,
            'waktu_akhir_kerja' => $waktuAkhirKerja,
        ];

        // Simpan data ke database
        AbsenKerja::create($data);

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Absen berhasil ditambahkan.');
    }
    
    public function selesaiKerja($id)
    {
        $absen = AbsenKerja::findOrFail($id);
        $absen->update(['waktu_akhir_kerja' => now()]);

        return redirect()->back()->with('success', 'Waktu kerja diselesaikan.');
    }

    public function destroy($id)
    {
        $absen = AbsenKerja::findOrFail($id);
        $absen->delete();

        return redirect()->back()->with('success', 'Data absen dihapus.');
    }

    // untuk update status langsung pada table 
    // public function updateStatus(Request $request, $id)
    // {
    //     // Validasi status_masuk
    //     $request->validate([
    //         'status_masuk' => 'required|in:masuk,izin,alfa',
    //     ]);
    
    //     // Temukan absensi berdasarkan ID
    //     $absensi = AbsenKerja::findOrFail($id);
    //     $absensi->status_masuk = $request->status_masuk;
    //     $absensi->save();
    
    //     // Redirect kembali dengan pesan sukses
    //     return redirect()->back()->with('success', 'Status absensi berhasil diperbarui!');
    // }
    public function updateStatus(Request $request, $id)
    {
        // Temukan absensi berdasarkan ID
        $absensi = AbsenKerja::findOrFail($id);
    
        // Cek jika hanya ingin update status saja
        if ($request->has('status_only') && $request->status_only == true) {
            $request->validate([
                'status_masuk' => 'required|in:masuk,izin,alfa',
            ]);
    
            $absensi->status_masuk = $request->status_masuk;
            $absensi->save();
    
            return redirect()->back()->with('success', 'Status absensi berhasil diperbarui!');
        }
    
        // Kalau update dari modal (edit lengkap)
        $request->validate([
            'user_id' => 'required',
            'tanggal_masuk' => 'required|date',
            'status_masuk' => 'required|in:masuk,izin,alfa',
            'waktu_masuk' => 'required',
            'waktu_akhir_kerja' => 'required',
        ]);
    
        $absensi->user_id = $request->user_id;
        $absensi->tanggal_masuk = $request->tanggal_masuk;
        $absensi->status_masuk = $request->status_masuk;
        $absensi->waktu_masuk = $request->waktu_masuk;
        $absensi->waktu_akhir_kerja = $request->waktu_akhir_kerja;
        $absensi->save();
    
        return redirect()->back()->with('success', 'Data absensi berhasil diperbarui!');
    }
    


    public function exportExcel()
    {
        return Excel::download(new AbsensiExport, 'absensi.xlsx');
    }
    
    public function exportPDF()
    {
        $absensi = \App\Models\AbsenKerja::with('user')->get();
        return view('admin.absensi.absensi_pdf', compact('absensi'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_import' => 'required|mimes:xlsx,xls,csv',
        ]);
    
        try {
            $file = $request->file('file_import');
            Log::info("File upload diterima: " . $file->getClientOriginalName());
    
            Excel::import(new AbsensiImport, $file);
    
            return redirect()->route('absensi.index')->with('success', 'Data berhasil diimport');
        } catch (\Exception $e) {
            Log::error("Kesalahan saat import: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengimpor data.');
        }
    }
    
}
