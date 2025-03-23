<?php

namespace App\Exports;

use App\Models\Pelanggan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PelangganExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    private $index = 0; // Variabel untuk nomor urut otomatis

    /**
     * Mengambil data dari database dan mengembalikan sebagai koleksi
     */
    public function collection()
    {
        return Pelanggan::orderBy('id', 'asc')->get();
    }

    /**
     * Menentukan judul kolom untuk file Excel
     */
    public function headings(): array
    {
        return [
            'No', 
            'Kode Pelanggan',
            'Nama',
            'Alamat',
            'No Telepon',
            'Email'
        ];
    }

    /**
     * Memformat data sebelum diekspor ke Excel
     */
    public function map($pelanggan): array
    {
        $this->index++; // Menambahkan nomor urut otomatis

        return [
            $this->index, 
            $pelanggan->kode_pelanggan ?? '-',
            $pelanggan->nama ?? '-',
            $pelanggan->alamat ?? '-',
            $pelanggan->no_telp ?? '-',
            $pelanggan->email ?? '-',
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
