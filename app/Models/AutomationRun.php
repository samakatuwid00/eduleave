<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationRun extends Model
{
    protected $fillable = [
        'rule_code',
        'window_key',
        'idempotency_key',
        'status',
        'attempt',
        'audience_count',
        'item_count',
        'metadata',
        'started_at',
        'finished_at',
        'error_summary',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }
}
