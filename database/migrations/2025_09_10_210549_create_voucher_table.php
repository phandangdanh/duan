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
        Schema::create('voucher', function (Blueprint $table) {
            $table->id();
            $table->string('ma_voucher', 50)->unique()->comment('Mã voucher');
            $table->string('ten_voucher', 255)->comment('Tên voucher');
            $table->text('mota')->nullable();
            $table->enum('loai_giam_gia', ['phan_tram', 'tien_mat'])->comment('Loại giảm giá');
            $table->decimal('gia_tri', 10, 2)->comment('Giá trị giảm giá');
            $table->decimal('gia_tri_toi_thieu', 10, 2)->default(0)->comment('Giá trị đơn hàng tối thiểu');
            $table->decimal('gia_tri_toi_da', 10, 2)->nullable()->comment('Giá trị giảm tối đa');
            $table->integer('so_luong')->default(0)->comment('Số lượng voucher');
            $table->integer('so_luong_da_su_dung')->default(0)->comment('Số lượng đã sử dụng');
            $table->datetime('ngay_bat_dau')->comment('Ngày bắt đầu');
            $table->datetime('ngay_ket_thuc')->comment('Ngày kết thúc');
            $table->boolean('trang_thai')->default(true)->comment('Trạng thái hoạt động');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher');
    }
};