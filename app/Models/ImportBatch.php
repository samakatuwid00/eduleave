<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportBatch extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'admin_user_id',
        'employee_profile_id',
        'card_type',
        'parser_version',
        'original_name',
        'file_hash',
        'stored_path',
        'status',
        'row_count',
        'error_count',
        'preview_data',
        'expires_at',
        'committed_at',
        'rolled_back_at',
        'rollback_reason',
    ];

    protected function casts(): array
    {
        return [
            'preview_data' => 'array',
            'expires_at' => 'datetime',
            'committed_at' => 'datetime',
            'rolled_back_at' => 'datetime',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function employeeProfile(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id', 'user_id');
    }

    public function teachingLeaveCards(): HasMany
    {
        return $this->hasMany(TeachingLeaveCard::class);
    }

    public function nonTeachingLeaveCards(): HasMany
    {
        return $this->hasMany(NonTeachingLeaveCard::class);
    }
}
