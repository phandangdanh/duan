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
        Schema::create('donhang_voucher', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_donhang')->nullable();
            $table->unsignedBigInteger('id_voucher')->nullable();
            $table->datetime('ngayapdung')->nullable();
            
            $table->foreign('id_donhang')->references('id')->on('donhang')->onDelete('cascade');
            $table->foreign('id_voucher')->references('id')->on('voucher')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donhang_voucher');
    }
};