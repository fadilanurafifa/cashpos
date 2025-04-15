<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class ServiceThermal
{
    public function cetakStruk($transaksi)
    {
        try {
            Log::debug('Mencetak struk: mulai');

            $connector = new WindowsPrintConnector("POS-58");
            $printer = new Printer($connector);
            
            // Mengambil data pelanggan yang terkait dengan transaksi
            $pelanggan = $transaksi->pelanggan;
            
            // Mengecek apakah pelanggan adalah member
            if ($pelanggan && $pelanggan->is_member) {
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("Pelanggan: " . $pelanggan->nama . "\n"); // Menampilkan nama pelanggan jika member
            }
            
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Temu Rasa\n");
            $printer->text("Jl. Merdeka Belajar No. 12, Bandung Jawa Barat\n");
            $printer->text("Telp: 08123456789\n");
            $printer->text(date('d-m-Y H:i') . "\n");
            $printer->feed();
            
            // Menambahkan No Faktur dan Kasir
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("================================\n");
            
            $noFaktur = $transaksi->no_faktur ?? '-';
            $namaKasir = $transaksi->kasir->nama_kasir ?? '-';
            
            $printer->text("No Faktur: $noFaktur\n");
            $printer->text("Kasir: $namaKasir\n");
            
            $printer->text("================================\n");
            
            // Menampilkan daftar produk dengan jumlah dan harga
            $totalHarga = 0; // Variabel untuk menghitung total harga dari seluruh transaksi
            foreach ($transaksi->detail_penjualan as $item) {
                $nama = $item->produk->nama_produk;
                $qty = $item->jumlah;
                $harga = number_format($item->produk->harga, 0, ',', '.'); // Gunakan harga produk, bukan sub_total
                $subtotal = number_format($item->sub_total, 0, ',', '.'); // Gunakan sub_total untuk subtotal
                $totalHarga += $item->sub_total; // Menambahkan subtotal ke total harga
            
                // Menampilkan informasi produk
                $printer->text("$nama\n");
                $printer->text("$qty x Rp$harga = Rp$subtotal\n");
            }
            
            $printer->text("================================\n");
            
            // Hitung jumlah pembayaran dan kembalian
            $pembayaran = $transaksi->pembayaran ?? 0; // Mengambil pembayaran yang diterima (misalnya Rp100.000)
            $kembalian = $pembayaran - $totalHarga; // Menghitung kembalian
            
            $kembalianFormatted = number_format($kembalian, 0, ',', '.');
            $totalHargaFormatted = number_format($totalHarga, 0, ',', '.');
    

            $totals = $transaksi->total_bayar;
            $ppn = $totals * 0.11;
            $diskon = 0;

            $total_bayar = $totals + $ppn;

            Log::debug("Subtotal: Rp" . number_format($totals, 0, ',', '.'));
            Log::debug("PPN 11%: Rp" . number_format($ppn, 0, ',', '.'));
            Log::debug("Total Bayar: Rp" . number_format($total_bayar, 0, ',', '.'));

            $printer->text("Subtotal : Rp" . number_format($totals, 0, ',', '.') . "\n");
            $printer->text("PPN 11%  : Rp" . number_format($ppn, 0, ',', '.') . "\n");
            $printer->text("TOTAL    : Rp" . number_format($total_bayar, 0, ',', '.') . "\n");

            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Terima Kasih Atas Kunjungan Anda!\n");
            $printer->text("~ Temu Rasa ~\n");
            $printer->pulse();
            $printer->cut();
            $printer->close();

            Log::debug('Mencetak struk: selesai');
        } catch (\Exception $e) {
            Log::error('Gagal cetak struk: ' . $e->getMessage());
        }
    }
}