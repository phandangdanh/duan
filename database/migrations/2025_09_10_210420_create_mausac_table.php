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
        Schema::create('mausac', function (Blueprint $table) {
            $table->id();
            $table->string('ten', 100)->comment('Tên màu sắc');
            $table->string('mota', 255)->nullable()->comment('Mô tả màu sắc');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mausac');
    }
};
