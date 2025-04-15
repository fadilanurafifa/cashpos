<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanBarang extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_barang';

    protected $guarded = [];
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }
    // Method boot() akan dipanggil secara otomatis saat model diinisialisasi
    public static function boot() {
        // Memanggil method boot() dari parent class (Eloquent Model)
        parent::boot();

        // Menambahkan event listener untuk event "creating"
        // Event ini dipicu saat instance model akan dibuat (sebelum disimpan ke database)
        static::creating(function ($pengajuan) {
            // Saat model Pengajuan dibuat, otomatis mengisi field 'nama_pengaju'
            // dengan nilai 'nama' dari relasi pelanggan (misal $pengajuan->pelanggan)
            $pengajuan->nama_pengaju = $pengajuan->pelanggan->nama;
        });
    }
}
