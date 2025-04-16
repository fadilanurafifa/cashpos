<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Schema::create('activity_logs', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('user_id')->nullable();
        //     $table->string('action')->nullable();
        //     $table->string('table_name')->nullable();
        //     $table->unsignedBigInteger('record_id')->nullable();
        //     $table->text('old_data')->nullable();
        //     $table->text('new_data')->nullable();
        //     $table->ipAddress('ip_address')->nullable();
        //     $table->text('user_agent')->nullable();
        //     $table->timestamps();
        // });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

