<?php

namespace App\Models;

use App\Notifications\QueuedResetPassword;
use App\Notifications\QueuedVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const ROLE_RECORDS_ADMIN = 'records_admin';

    public const ROLE_AUDITOR = 'auditor';

    public const ADMIN_PERMISSIONS = [
        'manage_users',
        'manage_leave_cards',
        'manage_imports',
        'view_analytics',
        'export_reports',
        'manage_automation',
        'view_audit',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'processed_at' => 'datetime',
        ];
    }

    public function employeeProfile(): HasOne
    {
        return $this->hasOne(EmployeeProfile::class, 'user_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'processed_by');
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new QueuedVerifyEmail);
    }

    public function verificationEmailCooldownKey(): string
    {
        return 'verification-email:user:'.$this->getAuthIdentifier();
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new QueuedResetPassword($token));
    }

    public function dashboardRouteName(): string
    {
        if ($this->usertype === 'admin') {
            return 'admin.dashboard';
        }

        return $this->status === 'pending'
            ? '/user/dashboard/warning'
            : 'user/dashboard';
    }

    public function isAdmin(): bool
    {
        return $this->usertype === 'admin';
    }

    public function effectiveAdminRole(): ?string
    {
        if (! $this->isAdmin()) {
            return null;
        }

        return $this->admin_role ?: self::ROLE_SUPER_ADMIN;
    }

    public function isSuperAdmin(): bool
    {
        return $this->effectiveAdminRole() === self::ROLE_SUPER_ADMIN;
    }

    public function hasAdminPermission(string $permission): bool
    {
        if (! $this->isAdmin() || ! in_array($permission, self::ADMIN_PERMISSIONS, true)) {
            return false;
        }

        return match ($this->effectiveAdminRole()) {
            self::ROLE_SUPER_ADMIN => true,
            self::ROLE_RECORDS_ADMIN => in_array($permission, [
                'manage_users', 'manage_leave_cards', 'manage_imports', 'view_analytics', 'export_reports',
            ], true),
            self::ROLE_AUDITOR => in_array($permission, ['view_analytics', 'export_reports', 'view_audit'], true),
            default => false,
        };
    }

    public function Pending()
    {
        return $this->pending;  // Assumes 'is_admin' field exists
    }
}
