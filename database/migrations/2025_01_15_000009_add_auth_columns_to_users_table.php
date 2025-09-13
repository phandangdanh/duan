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
        Schema::table('users', function (Blueprint $table) {
            $table->string('mfa_secret')->nullable()->after('provider_id');
            $table->boolean('mfa_enabled')->default(false)->after('mfa_secret');
            $table->timestamp('last_login_at')->nullable()->after('mfa_enabled');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mfa_secret', 'mfa_enabled', 'last_login_at', 'last_login_ip']);
        });
    }
};
