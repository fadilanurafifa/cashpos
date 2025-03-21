<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->enum('status_pembayaran', ['pending', 'lunas'])->default('pending')->after('metode_pembayar');
        });
    }

    public function down(): void {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropColumn('status_pembayaran');
        });
    }
};
