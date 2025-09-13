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
        Schema::create('chitietdonhang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_donhang')->nullable();
            $table->unsignedBigInteger('id_chitietsanpham')->nullable();
            $table->string('tensanpham', 255)->nullable();
            $table->decimal('dongia', 15, 2)->nullable();
            $table->integer('soluong')->nullable();
            $table->decimal('thanhtien', 15, 2)->nullable();
            $table->text('ghichu')->nullable();
            
            $table->foreign('id_donhang')->references('id')->on('donhang')->onDelete('cascade');
            $table->foreign('id_chitietsanpham')->references('id')->on('chitietsanpham')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chitietdonhang');
    }
};