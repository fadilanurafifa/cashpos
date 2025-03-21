<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanBarang extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_barang';

    protected $fillable = [
        'pelanggan_id',
        'nama_pengaju',
        'nama_barang',
        'tanggal_pengajuan',
        'qty',
        'status',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }
    public static function boot() {
        parent::boot();
        static::creating(function ($pengajuan) {
            $pengajuan->nama_pengaju = $pengajuan->pelanggan->nama;
        });
    }
    
}
