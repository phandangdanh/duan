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
        Schema::create('donhang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->string('trangthai', 50)->nullable();
            $table->datetime('ngaytao')->nullable();
            $table->datetime('ngaythanhtoan')->nullable();
            $table->string('nhanvien', 255)->nullable();
            $table->string('tensp', 255)->nullable();
            $table->decimal('tongtien', 15, 2)->nullable();
            $table->text('ghichu')->nullable();
            $table->text('lichsutrangthai')->nullable();
            $table->string('phuongthucthanhtoan', 50)->default('COD')->comment('Phương thức thanh toán: COD, BANKING, MOMO, ZALOPAY');
            $table->string('trangthaithanhtoan', 50)->default('chua_thanh_toan')->comment('Trạng thái thanh toán: chua_thanh_toan, da_thanh_toan, hoan_tien');
            $table->text('diachigiaohang')->nullable()->comment('Địa chỉ giao hàng');
            $table->string('sodienthoai', 20)->nullable()->comment('Số điện thoại giao hàng');
            $table->string('email', 255)->nullable()->comment('Email khách hàng');
            $table->string('hoten', 255)->nullable()->comment('Họ tên khách hàng');
            
            $table->foreign('id_user')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donhang');
    }
};
