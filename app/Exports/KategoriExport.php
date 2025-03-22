<?php

namespace App\Exports;

use App\Models\Kategori;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class KategoriExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    private $index = 0; // Variabel untuk nomor urut otomatis

    /**
     * Mengambil data dari database dan mengembalikan sebagai koleksi
     */
    public function collection()
    {
        return Kategori::orderBy('id', 'asc')->get();
    }

    /**
     * Menentukan judul kolom untuk file Excel
     */
    public function headings(): array
    {
        return [
            'No', // Nomor urut
            'Nama Kategori' // Nama kategori produk
        ];
    }

    /**
     * Memformat data sebelum diekspor ke Excel
     */
    public function map($kategori): array
    {
        $this->index++; // Menambahkan nomor urut otomatis

        return [
            $this->index, // Menampilkan nomor urut
            $kategori->nama_kategori ?? '-', // Jika nama kategori kosong, tampilkan "-"
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

                // Membuat teks header (A1:B1) menjadi bold agar lebih jelas
                $sheet->getStyle('A1:B1')->getFont()->setBold(true);

                // Memberi warna latar belakang header (A1:B1) dengan biru muda
                $sheet->getStyle('A1:B1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('ADD8E6'); // Warna biru muda (light blue)

                // Membuat teks header rata tengah
                $sheet->getStyle('A1:B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
