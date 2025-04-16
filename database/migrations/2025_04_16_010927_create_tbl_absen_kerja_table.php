<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_absen_kerja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ID pegawai
            $table->enum('status_masuk', ['masuk', 'sakit', 'cuti']);
            $table->timestamp('waktu_masuk')->nullable();
            $table->timestamp('waktu_akhir_kerja')->nullable();
            $table->timestamps();
        
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_absen_kerja');
    }
};
