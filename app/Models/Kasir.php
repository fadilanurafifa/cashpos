<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kasir extends Model
{
    protected $table = 'kasir';

    protected $fillable = ['nama_kasir', 'slot_kasir'];

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }
}
