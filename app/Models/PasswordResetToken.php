<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordResetToken extends Model
{
    protected $table = 'password_reset_tokens';
    
    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    public $timestamps = false;

    protected $primaryKey = 'email';

    /**
     * Check if token is expired.
     */
    public function isExpired(int $expirationMinutes = 60): bool
    {
        return $this->created_at->addMinutes($expirationMinutes)->isPast();
    }

    /**
     * Find token by email and token.
     */
    public static function findByToken(string $email, string $token): ?self
    {
        return static::where('email', $email)
            ->where('token', $token)
            ->first();
    }

    /**
     * Delete expired tokens.
     */
    public static function deleteExpired(int $expirationMinutes = 60): int
    {
        return static::where('created_at', '<', Carbon::now()->subMinutes($expirationMinutes))
            ->delete();
    }
}
