<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->integer('code');
            $table->string('name', 255)->nullable();
            $table->string('name_en', 255)->nullable();
            $table->string('full_name', 255)->nullable();
            $table->string('full_name_en', 255)->nullable();
            $table->string('code_name', 255)->nullable();
            $table->integer('province_code')->nullable();
            $table->integer('administrative_unit_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
