<?php

namespace App\Imports;

use App\Models\Kategori;
use App\Models\Produk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProdukImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cari ID kategori berdasarkan nama
        $kategori = Kategori::where('nama_kategori', $row['nama_kategori'] ?? '')->first();
    
        // Skip baris jika kategori tidak ditemukan
        if (!$kategori) {
            return null;
        }
    
        return new Produk([
            'nama_produk' => $row['nama_produk'],
            'harga'       => $row['harga'],
            'stok'        => $row['stok'],
            'kategori_id' => $kategori->id,
            'foto'        => null,
        ]);
    }
}


