<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sanpham_hinhanh', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('sanpham_id');
            $table->string('url', 255);
            $table->integer('is_default')->nullable()->default('0');
            $table->text('mota')->nullable();
            $table->timestamp('created_at')->nullable()->default('current_timestamp()');
            $table->timestamp('updated_at')->nullable()->default('current_timestamp()');
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sanpham_hinhanh');
    }
};
