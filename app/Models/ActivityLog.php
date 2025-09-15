<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log user activity
     */
    public static function log(string $action, string $description = null, array $metadata = []): self
    {
        $user = auth()->user();
        
        // For failed login with existing user, get user from metadata
        if (!$user && $action === 'failed_login' && isset($metadata['user_id'])) {
            $user = \App\Models\User::find($metadata['user_id']);
        }
        
        return self::create([
            'user_id' => $user ? $user->id : null,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get action badge color
     */
    public function getActionBadgeColorAttribute(): string
    {
        return match($this->action) {
            'login' => 'bg-green-100 text-green-800',
            'logout' => 'bg-red-100 text-red-800',
            'profile_update' => 'bg-blue-100 text-blue-800',
            'password_change' => 'bg-yellow-100 text-yellow-800',
            'failed_login' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get action display name
     */
    public function getActionDisplayNameAttribute(): string
    {
        return match($this->action) {
            'login' => 'Login',
            'logout' => 'Logout',
            'profile_update' => 'Update Profil',
            'password_change' => 'Ubah Password',
            'failed_login' => 'Login Gagal',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }

    /**
     * Get formatted time
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at->format('d M Y, H:i:s');
    }
}