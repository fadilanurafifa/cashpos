<?php

namespace App\Exports;

use App\Models\PengajuanBarang;
use Maatwebsite\Excel\Concerns\FromCollection; // Menggunakan koleksi data dari database
use Maatwebsite\Excel\Concerns\WithHeadings; // Menambahkan header pada file Excel
use Maatwebsite\Excel\Concerns\WithMapping; // Memformat data sebelum diekspor
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Mengatur ukuran kolom otomatis
use Maatwebsite\Excel\Concerns\WithEvents; // Menangani event setelah sheet dibuat
use Maatwebsite\Excel\Events\AfterSheet; // Event untuk memodifikasi sheet setelah dibuat

class PengajuanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    private $index = 0; // Variabel untuk nomor urut otomatis

    /**
     * Mengambil data dari database dan mengembalikan sebagai koleksi
     * Data diurutkan berdasarkan tanggal pengajuan terbaru
     */
    public function collection()
    {
        return PengajuanBarang::orderBy('created_at', 'desc')->get();
    }

    /**
     * Menentukan judul kolom untuk file Excel
     */
    public function headings(): array
    {
        return [
            'No', // Nomor urut
            'Nama Pengaju', // Nama orang yang mengajukan
            'Nama Barang', // Nama barang yang diajukan
            'Tanggal Pengajuan', // Tanggal pengajuan dibuat
            'Jumlah', // Jumlah barang yang diajukan
            'Status' // Status pengajuan (misal: pending, approved, rejected)
        ];
    }

    /**
     * Memformat data sebelum diekspor ke Excel
     * - Menambahkan nomor urut
     * - Mengatur format tanggal menjadi "d-m-Y"
     * - Memastikan nilai default jika data kosong
     */
    public function map($pengajuan): array
    {
        $this->index++; // Menambahkan nomor urut otomatis

        return [
            $this->index, // Menampilkan nomor urut
            $pengajuan->nama_pengaju ?? '-', // Jika tidak ada nama, tampilkan "-"
            $pengajuan->nama_barang ?? '-', // Jika tidak ada nama barang, tampilkan "-"
            $pengajuan->created_at ? $pengajuan->created_at->format('d-m-Y') : '-', // Format tanggal atau "-"
            number_format($pengajuan->qty ?? 0, 0, ',', '.'), // Format angka jumlah barang tanpa desimal
            ucfirst($pengajuan->status) // Status dengan huruf pertama kapital
        ];
    }

    /**
     * Menangani event setelah sheet dibuat
     * - Membuat judul kolom (baris pertama) menjadi bold agar lebih jelas
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Membuat teks header (A1 sampai F1) menjadi tebal
                $event->sheet->getStyle('A1:F1')->getFont()->setBold(true);
            },
        ];
    }
}
