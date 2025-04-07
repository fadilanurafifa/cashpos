<?php

namespace App\Exports;

use App\Models\Penjualan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TransaksiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    private $index = 0; // Untuk nomor urut otomatis

    /**
     * Ambil semua data penjualan beserta relasi kasir dan pelanggan
     */
    public function collection()
    {
        return Penjualan::with('kasir', 'pelanggan')
            ->orderBy('id', 'asc') // ⬅️ ubah ke ASCENDING (1 ke atas)
            ->get();
    }

    /**
     * Judul kolom Excel
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama Pelanggan',
            'Jenis Pelanggan', 
            'ID Penjualan',
            'Nama Kasir',
            'Total Bayar',
            'Status',
            'Tanggal Transaksi',
        ];
    }

    /**
     * Format isi setiap baris data
     */
    public function map($item): array
    {
        $this->index++;

        return [
            $this->index,
            $item->pelanggan->nama ?? 'Biasa',
            $item->pelanggan ? 'Member' : 'Biasa',
            $item->id,
            $item->kasir->nama_kasir ?? '-',
            'Rp ' . number_format($item->total_bayar, 0, ',', '.'),
            ucfirst($item->status_pembayaran),
            $item->created_at->format('d-m-Y H:i'),
        ];
    }

    /**
     * Styling tambahan setelah sheet dibuat
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Buat header bold dan rata tengah
                $sheet->getStyle('A1:G1')->getFont()->setBold(true);
                $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Warna latar header (light blue)
                $sheet->getStyle('A1:G1')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('ADD8E6');
            },
        ];
    }
}
