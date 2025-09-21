<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donhang_voucher', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('id_donhang')->nullable();
            $table->integer('id_voucher')->nullable();
            $table->dateTime('ngayapdung')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donhang_voucher');
    }
};
