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
    private $index = 0; // Nomor urut otomatis

    protected $kategori_id;

    public function __construct($kategori_id)
    {
        $this->kategori_id = $kategori_id;
    }

    /**
     * Mengambil data dari database
     */
    public function collection()
    {
        return Produk::when($this->kategori_id, function ($query) {
            $query->where('kategori_id', $this->kategori_id);
        })->orderBy('id', 'asc')->get();
    }

    /**
     * Menentukan judul kolom untuk file Excel
     */
    public function headings(): array
    {
        return [
            'No', // Nomor urut
            'Nama Produk',
            'Stok Awal',
            'Terjual',
            'Keuntungan'
        ];
    }

    /**
     * Memformat data sebelum diekspor ke Excel
     */
    public function map($produk): array
    {
        $this->index++; // Tambah nomor urut

        $stok_awal = 100; // Bisa disesuaikan
        $terjual = $produk->detailPenjualan
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('jumlah');

        $keuntungan = $terjual * $produk->harga;

        return [
            $this->index, // Nomor urut otomatis
            $produk->nama_produk ?? '-',
            $stok_awal,
            $terjual,
            number_format($keuntungan, 0, ',', '.'),
        ];
    }

    /**
     * Mengatur gaya tampilan setelah sheet dibuat
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Membuat teks header (A1:E1) bold dan rata tengah
                $sheet->getStyle('A1:E1')->getFont()->setBold(true);
                $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Memberi warna latar belakang header (A1:E1) dengan biru muda
                $sheet->getStyle('A1:E1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('ADD8E6'); // Warna biru muda (light blue)
            },
        ];
    }
}
