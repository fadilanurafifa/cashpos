<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('pengajuan_barang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pelanggan_id'); // Relasi ke tabel pelanggan
            $table->string('nama_pengaju')->nullable();
            $table->string('nama_barang');
            $table->date('tanggal_pengajuan');
            $table->integer('qty'); // Tambahkan field qty
            $table->boolean('terpenuhi')->default(0); // 0 = belum terpenuhi, 1 = terpenuhi
            $table->timestamps();
    
            // Hubungkan ke tabel pelanggan
            $table->foreign('pelanggan_id')->references('id')->on('pelanggan')->onDelete('cascade');
        });
    }   

    public function down() {
        Schema::dropIfExists('pengajuan_barang');
    }
};
