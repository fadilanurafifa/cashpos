<?php

namespace App\Imports;

use App\Models\Pelanggan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PelangganImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Pelanggan([
            'kode_pelanggan' => $row['kode_pelanggan'],
            'nama'           => $row['nama'],
            'alamat'         => $row['alamat'],
            'no_telp'        => $row['no_telp'],
            'email'          => $row['email'],
        ]);
    }
}

