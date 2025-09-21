<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('danhgia', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('id_chitietdonhang')->nullable();
            $table->integer('sao')->nullable();
            $table->text('noidung')->nullable();
            $table->dateTime('thoigian')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('danhgia');
    }
};
