<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'provider_name')) {
                $table->string('provider_name', 32)->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'provider_id')) {
                $table->string('provider_id', 191)->nullable()->after('provider_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'provider_id')) {
                $table->dropColumn('provider_id');
            }
            if (Schema::hasColumn('users', 'provider_name')) {
                $table->dropColumn('provider_name');
            }
        });
    }
};


