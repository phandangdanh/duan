<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'ip_address',
        'user_agent',
        'last_activity',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'last_activity' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if session is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Update last activity.
     */
    public function updateActivity(): void
    {
        $this->update(['last_activity' => now()]);
    }

    /**
     * Deactivate session.
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}
