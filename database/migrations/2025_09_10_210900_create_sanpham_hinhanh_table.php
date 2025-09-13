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
        Schema::create('sanpham_hinhanh', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_sp');
            $table->string('hinhanh', 255);
            $table->integer('loai')->default(1)->comment('1: ảnh chính, 2: ảnh phụ');
            $table->timestamps();
            
            $table->foreign('id_sp')->references('id')->on('sanpham')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanpham_hinhanh');
    }
};
