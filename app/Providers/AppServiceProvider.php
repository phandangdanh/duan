<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Services\Interfaces\UserServiceInterface::class,
            \App\Services\UserService::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\ProvinceRepositoryInterface::class,
            \App\Repositories\ProvinceRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\DistrictRepositoryInterface::class,
            \App\Repositories\DistrictRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\WardRepositoryInterface::class,
            \App\Repositories\WardRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\BaseRepositoryInterface::class,
            \App\Repositories\BaseRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\UserRepositoryInterface::class,
            \App\Repositories\UserRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\SanPhamRepositoryInterface::class,
            \App\Repositories\SanPhamRepository::class
        );
        $this->app->bind(
            \App\Services\Interfaces\SanPhamServiceInterface::class,
            \App\Services\SanPhamService::class
        );
        $this->app->bind(
            \App\Services\Interfaces\AuthServiceInterface::class,
            \App\Services\AuthService::class
        );
        
        // Auth Repository bindings
        $this->app->bind(
            \App\Repositories\Interfaces\AuthRepositoryInterface::class,
            \App\Repositories\AuthRepository::class
        );
        
        // Role Repository bindings
        $this->app->bind(
            \App\Repositories\Interfaces\RoleRepositoryInterface::class,
            \App\Repositories\RoleRepository::class
        );
        
        // Permission Repository bindings
        $this->app->bind(
            \App\Repositories\Interfaces\PermissionRepositoryInterface::class,
            \App\Repositories\PermissionRepository::class
        );
        
        // Audit Log Repository bindings
        $this->app->bind(
            \App\Repositories\Interfaces\AuditLogRepositoryInterface::class,
            \App\Repositories\AuditLogRepository::class
        );
        
        // Service bindings
        $this->app->bind(
            \App\Services\Interfaces\RoleServiceInterface::class,
            \App\Services\RoleService::class
        );
        
        $this->app->bind(
            \App\Services\Interfaces\PermissionServiceInterface::class,
            \App\Services\PermissionService::class
        );
        
        $this->app->bind(
            \App\Services\Interfaces\AuditLogServiceInterface::class,
            \App\Services\AuditLogService::class
        );
    }

    public function boot(): void
    {
        //
    }
}