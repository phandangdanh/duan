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
        Schema::create('danhgia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_sp')->comment('ID sản phẩm');
            $table->unsignedBigInteger('id_user')->comment('ID người dùng');
            $table->integer('sao')->default(5)->comment('Số sao đánh giá (1-5)');
            $table->text('noidung')->nullable()->comment('Nội dung đánh giá');
            $table->string('hinhanh', 255)->nullable()->comment('Hình ảnh đánh giá');
            $table->integer('trangthai')->default(1)->comment('Trạng thái đánh giá');
            $table->timestamps();
            
            $table->foreign('id_sp')->references('id')->on('sanpham')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            
            // Unique constraint: một user chỉ đánh giá một sản phẩm một lần
            $table->unique(['id_sp', 'id_user']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danhgia');
    }
};
