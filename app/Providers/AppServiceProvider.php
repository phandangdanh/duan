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
        // Optional bindings: guard with class_exists at resolve-time to avoid static analysis errors
        
        // Auth Repository bindings
        // Optional bindings: guard with class_exists at resolve-time to avoid static analysis errors
        
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
        
        // Cart Service binding
        $this->app->bind(
            \App\Services\CartService::class,
            \App\Services\CartService::class
        );
        // Dynamically apply mail profile from config/mail_profiles.php
        $profileKey = config('mail_profiles.default_profile');
        $profile = config('mail_profiles.profiles.' . $profileKey);
        if (is_array($profile)) {
            config([
                'mail.default' => $profile['default'] ?? config('mail.default'),
                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.host' => $profile['host'] ?? config('mail.mailers.smtp.host'),
                'mail.mailers.smtp.port' => $profile['port'] ?? config('mail.mailers.smtp.port'),
                'mail.mailers.smtp.encryption' => $profile['encryption'] ?? config('mail.mailers.smtp.encryption'),
                'mail.mailers.smtp.username' => $profile['username'] ?? config('mail.mailers.smtp.username'),
                'mail.mailers.smtp.password' => $profile['password'] ?? config('mail.mailers.smtp.password'),
                'mail.from.address' => $profile['from_address'] ?? config('mail.from.address'),
                'mail.from.name' => $profile['from_name'] ?? config('mail.from.name'),
            ]);
        }
    }

    public function boot(): void
    {
        // Thiết lập ngôn ngữ mặc định là tiếng Việt
        app()->setLocale('vi');
        
        // Tùy chỉnh thông báo đặt lại mật khẩu
        \Illuminate\Auth\Notifications\ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
            
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Đặt lại mật khẩu')
                ->greeting('Xin chào!')
                ->line('Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.')
                ->action('Đặt lại mật khẩu', $url)
                ->line('Liên kết đặt lại mật khẩu này sẽ hết hạn trong 60 phút.')
                ->line('Nếu bạn không yêu cầu đặt lại mật khẩu, bạn không cần thực hiện thêm hành động nào.')
                ->salutation('Trân trọng,');
        });
    }
}