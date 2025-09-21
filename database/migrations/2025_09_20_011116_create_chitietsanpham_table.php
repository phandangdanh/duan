<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chitietsanpham', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('id_sp')->nullable();
            $table->integer('id_mausac')->nullable();
            $table->integer('id_size')->nullable();
            $table->integer('soLuong')->nullable()->default('0');
            $table->string('tenSp', 255)->nullable();
            $table->decimal('gia')->nullable();
            $table->decimal('gia_khuyenmai')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chitietsanpham');
    }
};
