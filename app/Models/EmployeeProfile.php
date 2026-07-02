<?php

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeProfile extends Model
{
    use HasFactory;

    protected $table = 'employee_profiles';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'employee_number',
        'personnel_type_id',
        'position',
        'date_employed',
        'sex',
        'date_of_birth',
        'place_of_birth',
        'station',
        'civil_status',
    ];

    protected function casts(): array
    {
        return [
            'date_employed' => 'date',
            'date_of_birth' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function personnelType(): BelongsTo
    {
        return $this->belongsTo(PersonnelType::class, 'personnel_type_id', 'id');
    }

    public function teachingLeaveCards(): HasMany
    {
        return $this->hasMany(TeachingLeaveCard::class, 'employee_profile_id', 'user_id');
    }

    public function nonTeachingLeaveCards(): HasMany
    {
        return $this->hasMany(NonTeachingLeaveCard::class, 'employee_profile_id', 'user_id');
    }

    /**
     * Resolve the leave-card model used by this employee's personnel type.
     *
     * @return class-string<TeachingLeaveCard|NonTeachingLeaveCard>
     */
    public function leaveCardModelClass(): string
    {
        $code = $this->personnelType?->code;

        if ($code === null) {
            throw new DomainException(
                "Employee profile {$this->getKey()} has no personnel type."
            );
        }

        return PersonnelType::leaveCardModelClass($code);
    }

    /**
     * Build a leave-card query for one hydrated employee profile.
     *
     * This is intentionally not an Eloquent relationship because mixed
     * personnel types cannot share one eager-loadable target table.
     *
     * @return Builder<TeachingLeaveCard|NonTeachingLeaveCard>
     */
    public function leaveCardQuery(): Builder
    {
        if (! $this->exists) {
            throw new DomainException('The employee profile must be persisted before querying leave cards.');
        }

        $modelClass = $this->leaveCardModelClass();

        return $modelClass::query()
            ->where('employee_profile_id', $this->getKey());
    }
}
