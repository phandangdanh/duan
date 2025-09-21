<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voucher', function (Blueprint $table) {
            $table->integer('id');
            $table->string('ma_voucher', 50)->unique();
            $table->string('ten_voucher', 255);
            $table->string('loai_giam_gia');
            $table->decimal('gia_tri');
            $table->decimal('gia_tri_toi_thieu')->default('0.00');
            $table->decimal('gia_tri_toi_da')->nullable();
            $table->integer('so_luong')->default('0');
            $table->integer('so_luong_da_su_dung')->default('0');
            $table->dateTime('ngay_bat_dau');
            $table->dateTime('ngay_ket_thuc');
            $table->integer('trang_thai')->default('1');
            $table->text('mota')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher');
    }
};
