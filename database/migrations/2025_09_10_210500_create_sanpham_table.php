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
        Schema::create('sanpham', function (Blueprint $table) {
            $table->id();
            $table->string('tenSP', 255);
            $table->string('maSP', 50)->unique();
            $table->text('mota')->nullable();
            $table->decimal('gia', 15, 2)->default(0);
            $table->decimal('gia_khuyenmai', 15, 2)->default(0);
            $table->string('hinhanh', 255)->nullable();
            $table->integer('id_danhmuc')->nullable();
            $table->integer('trangthai')->default(1);
            $table->integer('soLuong')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('id_danhmuc')->references('id')->on('danhmuc')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanpham');
    }
};
