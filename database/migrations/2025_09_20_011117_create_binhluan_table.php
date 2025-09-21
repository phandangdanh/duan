<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('binhluan', function (Blueprint $table) {
            $table->integer('id');
            $table->text('noidung')->nullable();
            $table->integer('id_sp')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->text('hinhanh')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('binhluan');
    }
};
