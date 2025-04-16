<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::create('activity_logs', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Bisa null jika user sudah dihapus
        //     $table->string('action'); // Contoh: 'create', 'update', 'delete'
        //     $table->string('table_name'); // Nama tabel yang dimodifikasi
        //     $table->unsignedBigInteger('record_id')->nullable(); // ID data yang dimodifikasi
        //     $table->json('old_data')->nullable(); // Data sebelum perubahan
        //     $table->json('new_data')->nullable(); // Data setelah perubahan
        //     $table->ipAddress('ip_address')->nullable();
        //     $table->text('user_agent')->nullable(); // Info browser/user agent
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
}
