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
        Schema::table('tbl_absen_kerja', function (Blueprint $table) {
            $table->date('tanggal_masuk')->after('user_id')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('tbl_absen_kerja', function (Blueprint $table) {
            $table->dropColumn('tanggal_masuk');
        });
    }
    
};
