<?php

namespace App\Imports;

use App\Models\AbsenKerja;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AbsensiImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        Log::info("Memproses baris import: ", $row);

        if (empty($row['user_id']) || empty($row['tanggal_masuk']) || empty($row['waktu_masuk'])) {
            Log::warning("Data tidak lengkap, baris diabaikan.");
            return null;
        }

        $user = User::find($row['user_id']);
        if (!$user) {
            Log::warning("User ID tidak ditemukan: " . $row['user_id']);
            return null;
        }

        try {
            $tanggalMasuk = Carbon::parse($row['tanggal_masuk']);
        } catch (\Exception $e) {
            Log::error("Format tanggal salah: {$row['tanggal_masuk']}");
            return null;
        }

        try {
            $waktuMasuk = Carbon::parse($row['waktu_masuk']);
        } catch (\Exception $e) {
            Log::error("Format waktu salah: {$row['waktu_masuk']}");
            return null;
        }

        try {
            $waktuAkhirKerja = isset($row['waktu_akhir_kerja']) && $row['waktu_akhir_kerja'] !== null
                ? Carbon::parse($row['waktu_akhir_kerja'])
                : null;
        } catch (\Exception $e) {
            Log::error("Format waktu akhir salah: {$row['waktu_akhir_kerja']}");
            $waktuAkhirKerja = null;
        }

        return new AbsenKerja([
            'user_id'           => $row['user_id'],
            'tanggal_masuk'     => $tanggalMasuk,
            'status_masuk'      => $row['status_masuk'],
            'waktu_masuk'       => $waktuMasuk,
            'waktu_akhir_kerja' => $waktuAkhirKerja,
        ]);
    }
}
