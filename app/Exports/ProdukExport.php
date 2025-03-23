<?php

namespace App\Exports;

use App\Models\Produk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ProdukExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    private $index = 0; // Untuk nomor urut otomatis

    /**
     * Mengambil data dari database
     */
    public function collection()
    {
        return Produk::orderBy('id', 'asc')->get();
    }

    /**
     * Menentukan judul kolom untuk file Excel
     */
    public function headings(): array
    {
        return [
            'No', // Nomor urut
            'Nama Produk',
            'Harga',
            'Stok',
            'Tanggal Dibuat',
            'Tanggal Diperbarui'
        ];
    }

    /**
     * Memformat data sebelum diekspor ke Excel
     */
    public function map($produk): array
    {
        $this->index++; // Menambahkan nomor urut otomatis

        return [
            $this->index, // Menampilkan nomor urut
            $produk->nama_produk ?? '-',
            'Rp ' . number_format($produk->harga, 0, ',', '.'), // Format harga ke Rupiah
            $produk->stok ?? 0,
            $produk->created_at->format('d-m-Y H:i'),
            $produk->updated_at->format('d-m-Y H:i'),
        ];
    }

    /**
     * Menangani event setelah sheet dibuat
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Membuat teks header (A1:F1) menjadi bold agar lebih jelas
                $sheet->getStyle('A1:F1')->getFont()->setBold(true);

                // Memberi warna latar belakang header (A1:F1) dengan biru muda
                $sheet->getStyle('A1:F1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('ADD8E6'); // Warna biru muda (light blue)

                // Membuat teks header rata tengah
                $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}


