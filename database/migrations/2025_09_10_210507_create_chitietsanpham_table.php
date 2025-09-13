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
        Schema::create('chitietsanpham', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_sp')->comment('ID sản phẩm');
            $table->unsignedBigInteger('id_mausac')->nullable()->comment('ID màu sắc');
            $table->unsignedBigInteger('id_size')->nullable()->comment('ID size');
            $table->integer('soLuong')->default(0)->comment('Số lượng tồn kho');
            $table->string('tenSp', 255)->nullable()->comment('Tên chi tiết sản phẩm');
            $table->decimal('gia', 15, 2)->default(0)->comment('Giá bán');
            $table->decimal('gia_khuyenmai', 15, 2)->default(0)->comment('Giá khuyến mãi');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('id_sp')->references('id')->on('sanpham')->onDelete('cascade');
            $table->foreign('id_mausac')->references('id')->on('mausac')->onDelete('set null');
            $table->foreign('id_size')->references('id')->on('size')->onDelete('set null');
            
            // Indexes
            $table->index(['id_sp', 'id_mausac', 'id_size']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chitietsanpham');
    }
};
