<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo user admin
        UserModel::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone' => '0901234567',
            'address' => '123 Admin Street',
            'email_verified_at' => now(),
            'status' => 1,
            'user_catalogue_id' => 1, // 1 = admin
        ]);

        // Tạo user thường
        UserModel::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'phone' => '0907654321',
            'address' => '456 User Street',
            'email_verified_at' => now(),
            'status' => 1,
            'user_catalogue_id' => 2, // 2 = user thường
        ]);
    }
}
