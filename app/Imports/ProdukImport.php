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
        $kategori = Kategori::where('nama_kategori', $row['nama_kategori'] ?? null)->first();

        return new Produk([
            'nama_produk' => $row['nama_produk'] ?? null,
            'harga'       => $row['harga'] ?? 0,
            'stok'        => $row['stok'] ?? 0,
            'kategori_id' => $row['kategori_id'],
            'foto'        => null,
        ]);
    }
}


