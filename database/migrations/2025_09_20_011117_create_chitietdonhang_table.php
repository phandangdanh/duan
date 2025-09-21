<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chitietdonhang', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('id_donhang')->nullable();
            $table->integer('id_chitietsanpham')->nullable();
            $table->string('tensanpham', 255)->nullable();
            $table->decimal('dongia')->nullable();
            $table->integer('soluong')->nullable();
            $table->decimal('thanhtien')->nullable();
            $table->text('ghichu')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chitietdonhang');
    }
};
