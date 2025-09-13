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
        Schema::create('binhluan', function (Blueprint $table) {
            $table->id();
            $table->text('noidung')->nullable();
            $table->unsignedBigInteger('id_sp')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->text('hinhanh')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            
            $table->foreign('id_sp')->references('id')->on('sanpham')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('binhluan');
    }
};
