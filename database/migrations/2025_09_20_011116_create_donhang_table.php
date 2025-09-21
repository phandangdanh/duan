<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donhang', function (Blueprint $table) {
            $table->integer('id');
            $table->bigInteger('id_user')->nullable();
            $table->string('trangthai', 50)->nullable();
            $table->dateTime('ngaytao')->nullable();
            $table->dateTime('ngaythanhtoan')->nullable();
            $table->string('nhanvien', 255)->nullable();
            $table->string('tensp', 255)->nullable();
            $table->decimal('tongtien')->nullable();
            $table->text('ghichu')->nullable();
            $table->text('lichsutrangthai')->nullable();
            $table->string('phuongthucthanhtoan', 50)->nullable()->default('COD');
            $table->string('trangthaithanhtoan', 50)->nullable()->default('chua_thanh_toan');
            $table->text('diachigiaohang')->nullable();
            $table->string('sodienthoai', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('hoten', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donhang');
    }
};
