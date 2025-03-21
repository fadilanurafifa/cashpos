<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // ID User yang melakukan aksi
            $table->string('action'); // Aksi yang dilakukan
            $table->string('table_name')->nullable(); // Nama tabel yang terpengaruh
            $table->string('record_id')->nullable(); // ID data yang diubah
            $table->text('old_data')->nullable(); // Data sebelum perubahan
            $table->text('new_data')->nullable(); // Data setelah perubahan
            $table->ipAddress('ip_address')->nullable(); // IP pengguna
            $table->string('user_agent')->nullable(); // Browser / device info
            $table->timestamps();
    
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
