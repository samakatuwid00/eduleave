<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'personnel_type_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function personnelType(): BelongsTo
    {
        return $this->belongsTo(PersonnelType::class);
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
