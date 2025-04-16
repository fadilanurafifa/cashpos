<?php

namespace App\Exports;

use App\Models\AbsenKerja;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AbsensiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    private $index = 0;

    /**
     * Ambil data absensi beserta relasi user
     */
    public function collection()
    {
        return AbsenKerja::with('user')->orderBy('tanggal_masuk', 'desc')->get();
    }

    /**
     * Judul kolom
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Tanggal Masuk',
            'Status',
            'Waktu Masuk',
            'Waktu Selesai',
        ];
    }

    /**
     * Mapping data untuk setiap baris Excel
     */
    public function map($absensi): array
    {
        $this->index++;

        return [
            $this->index,
            $absensi->user->name ?? '-',
            $absensi->tanggal_masuk ?? '-',
            ucfirst($absensi->status_masuk ?? '-'),
            $absensi->waktu_masuk ?? '-',
            $absensi->waktu_akhir_kerja ?? '-',
        ];
    }

    /**
     * Styling setelah sheet dibuat
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Bold & warna header
                $sheet->getStyle('A1:F1')->getFont()->setBold(true);
                $sheet->getStyle('A1:F1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('ADD8E6');

                // Rata tengah untuk header
                $sheet->getStyle('A1:F1')->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
