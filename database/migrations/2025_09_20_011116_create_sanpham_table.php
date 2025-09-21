<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sanpham', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('maSP', 100)->nullable();
            $table->string('tenSP', 255)->nullable();
            $table->integer('id_danhmuc')->nullable();
            $table->text('moTa')->nullable();
            $table->decimal('base_price')->nullable();
            $table->decimal('base_sale_price')->nullable();
            $table->integer('trangthai')->default('1');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sanpham');
    }
};
