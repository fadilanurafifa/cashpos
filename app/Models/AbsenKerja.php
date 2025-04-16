<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsenKerja extends Model
{
    use HasFactory;

    protected $table = 'tbl_absen_kerja';

    protected $fillable = [
        'user_id',
        'tanggal_masuk',
        'status_masuk',
        'waktu_masuk',
        'waktu_akhir_kerja',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
     

}

