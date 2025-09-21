<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserModel extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'province_id',
        'district_id',
        'ward_id',
        'address',
        'birthday',
        'image',
        'description',
        'status',
        'user_agent',
        'ip',
        'email_verified_at',
        'remember_token',
        'google_id',
        'avatar',
        'provider',
        'user_catalogue_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function donHangs(): HasMany
    {
        return $this->hasMany(DonHang::class, 'id_user');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }

    /**
     * Lấy tất cả vai trò của người dùng
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Kiểm tra xem người dùng có vai trò cụ thể không
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    /**
     * Kiểm tra xem người dùng có quyền cụ thể không
     */
    public function hasPermission(string $permissionSlug): bool
    {
        // Sử dụng eager loading để tải permissions
        $userRoles = $this->roles()->with('permissions')->get();
        
        foreach ($userRoles as $role) {
            foreach ($role->permissions as $permission) {
                if ($permission->slug === $permissionSlug) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Kiểm tra xem tài khoản có đang hoạt động không
     */
    public function isActive(): bool
    {
        return $this->status == 1;
    }

    /**
     * Kiểm tra xem email đã được xác minh chưa
     */
    public function isEmailVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Kiểm tra xem người dùng có phải là admin không
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
}