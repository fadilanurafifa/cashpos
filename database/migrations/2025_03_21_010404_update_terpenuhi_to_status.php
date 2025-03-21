<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('pengajuan_barang', function (Blueprint $table) {
            $table->enum('status', ['terpenuhi', 'tidak terpenuhi'])->default('tidak terpenuhi')->change();
        });
    }

    public function down() {
        Schema::table('pengajuan_barang', function (Blueprint $table) {
            $table->tinyInteger('terpenuhi')->default(0)->change();
        });
    }
};
