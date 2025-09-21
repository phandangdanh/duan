<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo vai trò
        $adminRole = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Quản trị viên với toàn quyền truy cập',
        ]);

        $managerRole = Role::create([
            'name' => 'Manager',
            'slug' => 'manager',
            'description' => 'Quản lý với quyền truy cập hạn chế',
        ]);

        $userRole = Role::create([
            'name' => 'User',
            'slug' => 'user',
            'description' => 'Người dùng thông thường',
        ]);

        // Tạo quyền
        $permissions = [
            // Quyền quản lý người dùng
            [
                'name' => 'Xem người dùng',
                'slug' => 'view_users',
                'description' => 'Có thể xem danh sách người dùng',
            ],
            [
                'name' => 'Tạo người dùng',
                'slug' => 'create_users',
                'description' => 'Có thể tạo người dùng mới',
            ],
            [
                'name' => 'Cập nhật người dùng',
                'slug' => 'update_users',
                'description' => 'Có thể cập nhật thông tin người dùng',
            ],
            [
                'name' => 'Xóa người dùng',
                'slug' => 'delete_users',
                'description' => 'Có thể xóa người dùng',
            ],
            
            // Quyền quản lý danh mục
            [
                'name' => 'Quản lý danh mục',
                'slug' => 'manage_categories',
                'description' => 'Có thể quản lý danh mục sản phẩm',
            ],
            
            // Quyền quản lý sản phẩm
            [
                'name' => 'Quản lý sản phẩm',
                'slug' => 'manage_products',
                'description' => 'Có thể quản lý sản phẩm',
            ],
            
            // Quyền quản lý đơn hàng
            [
                'name' => 'Xem đơn hàng',
                'slug' => 'view_orders',
                'description' => 'Có thể xem danh sách đơn hàng',
            ],
            [
                'name' => 'Cập nhật đơn hàng',
                'slug' => 'update_orders',
                'description' => 'Có thể cập nhật trạng thái đơn hàng',
            ],
            [
                'name' => 'Xóa đơn hàng',
                'slug' => 'delete_orders',
                'description' => 'Có thể xóa đơn hàng',
            ],
            
            // Quyền quản lý voucher
            [
                'name' => 'Quản lý voucher',
                'slug' => 'manage_vouchers',
                'description' => 'Có thể quản lý voucher',
            ],
        ];

        foreach ($permissions as $permissionData) {
            Permission::create($permissionData);
        }

        // Gán quyền cho vai trò Admin
        $adminRole->permissions()->attach(Permission::all());

        // Gán quyền cho vai trò Manager
        $managerRole->permissions()->attach(Permission::whereIn('slug', [
            'view_users',
            'manage_categories',
            'manage_products',
            'view_orders',
            'update_orders',
            'manage_vouchers',
        ])->get());

        // Gán quyền cho vai trò User
        $userRole->permissions()->attach(Permission::whereIn('slug', [
            'view_orders',
        ])->get());

        // Tìm hoặc tạo người dùng admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'status' => 1,
            ]
        );

        // Gán vai trò Admin cho người dùng admin
        $admin->roles()->sync([$adminRole->id]);
    }
}
