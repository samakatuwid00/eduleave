<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationSetting extends Model
{
    protected $fillable = [
        'version',
        'automation_enabled',
        'daily_digest_enabled',
        'weekly_summary_enabled',
        'employee_notifications_enabled',
        'recipient_emails',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'automation_enabled' => 'boolean',
            'daily_digest_enabled' => 'boolean',
            'weekly_summary_enabled' => 'boolean',
            'employee_notifications_enabled' => 'boolean',
            'recipient_emails' => 'array',
        ];
    }

    public static function current(): self
    {
        return self::query()->firstOrCreate(['id' => 1]);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
