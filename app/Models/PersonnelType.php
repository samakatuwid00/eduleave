<?php

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PersonnelType extends Model
{
    use HasFactory;

    public const CODE_TEACHING = 'teaching';

    public const CODE_NON_TEACHING = 'non_teaching';

    protected $table = 'personnel_types';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
    ];

    /**
     * @return class-string<TeachingLeaveCard|NonTeachingLeaveCard>
     */
    public static function leaveCardModelClass(string $code): string
    {
        return match ($code) {
            self::CODE_TEACHING => TeachingLeaveCard::class,
            self::CODE_NON_TEACHING => NonTeachingLeaveCard::class,
            default => throw new DomainException("Unsupported personnel type [{$code}]."),
        };
    }

    public function employeeProfiles(): HasMany
    {
        return $this->hasMany(EmployeeProfile::class, 'personnel_type_id', 'id');
    }
}
