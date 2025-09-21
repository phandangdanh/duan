<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mausac', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('ten', 100);
            $table->string('mota', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mausac');
    }
};
