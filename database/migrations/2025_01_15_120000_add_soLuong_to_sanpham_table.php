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
        Schema::table('sanpham', function (Blueprint $table) {
            $table->integer('soLuong')->default(1)->after('base_sale_price')->comment('Số lượng sản phẩm chính');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sanpham', function (Blueprint $table) {
            $table->dropColumn('soLuong');
        });
    }
};
