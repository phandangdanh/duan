<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $adminUser = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'status' => 1,
        ]);

        // Assign admin role
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $adminUser->assignRole($adminRole);
        }

        // Create manager user
        $managerUser = User::create([
            'name' => 'Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'status' => 1,
        ]);

        // Assign manager role
        $managerRole = Role::where('slug', 'manager')->first();
        if ($managerRole) {
            $managerUser->assignRole($managerRole);
        }

        // Create customer user
        $customerUser = User::create([
            'name' => 'Customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'status' => 1,
        ]);

        // Assign customer role
        $customerRole = Role::where('slug', 'customer')->first();
        if ($customerRole) {
            $customerUser->assignRole($customerRole);
        }

        // Create additional test users
        for ($i = 1; $i <= 17; $i++) {
            $user = User::create([
                'name' => "Test User $i",
                'email' => "test$i@example.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'status' => 1,
            ]);
            
            $customerRole = Role::where('slug', 'customer')->first();
            if ($customerRole) {
                $user->assignRole($customerRole);
            }
        }
    }
}
