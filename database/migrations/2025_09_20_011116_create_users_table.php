<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 225);
            $table->string('phone', 20)->nullable();
            $table->integer('province_id')->nullable();
            $table->integer('district_id')->nullable();
            $table->integer('ward_id')->nullable();
            $table->string('address', 225)->nullable();
            $table->string('birthday', 225)->nullable();
            $table->string('image', 225)->nullable();
            $table->string('description', 225)->nullable();
            $table->integer('status')->nullable()->default('1');
            $table->string('user_agent', 225)->nullable();
            $table->string('ip', 225)->nullable();
            $table->string('email', 225)->unique()->nullable();
            $table->string('google_id', 255)->nullable();
            $table->string('avatar', 255)->nullable();
            $table->string('provider', 255)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 225)->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->string('provider_name', 255)->nullable();
            $table->string('provider_id', 255)->nullable();
            $table->string('mfa_secret', 255)->nullable();
            $table->integer('mfa_enabled')->default('0');
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('user_catalogue_id')->default('1');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
