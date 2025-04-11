<?php

namespace App\Exports;

use App\Models\Produk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanPenjualanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    private $index = 0;

    protected $kategori_id;

    public function __construct($kategori_id)
    {
        $this->kategori_id = $kategori_id;
    }

    public function collection()
    {
        return Produk::with('detailPenjualan')->when($this->kategori_id, function ($query) {
            $query->where('kategori_id', $this->kategori_id);
        })->orderBy('id', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Produk',
            'Stok Awal',
            'Terjual',
            'Harga Pokok',
            'Harga Jual',
            'Keuntungan',
        ];
    }

    public function map($produk): array
    {
        $this->index++;

        $stok_awal = 100;
        $terjual = $produk->detailPenjualan
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('jumlah');

        $harga_pokok = $produk->harga_pokok ?? 0;
        $harga_jual = $produk->harga ?? 0;
        $keuntungan = $terjual * ($harga_jual - $harga_pokok);

        return [
            $this->index,
            $produk->nama_produk ?? '-',
            $stok_awal,
            $terjual,
            'Rp' . number_format($harga_pokok, 0, ',', '.'),
            'Rp' . number_format($harga_jual, 0, ',', '.'),
            'Rp' . number_format($keuntungan, 0, ',', '.'),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Bold dan center header
                $sheet->getStyle('A1:G1')->getFont()->setBold(true);
                $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Background header
                $sheet->getStyle('A1:G1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('ADD8E6');
            },
        ];
    }
}
