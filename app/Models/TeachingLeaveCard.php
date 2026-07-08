<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeachingLeaveCard extends Model
{
    use HasFactory;

    protected $table = 'teaching_leave_cards';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'employee_profile_id',
        'inclusive_period',
        'nature_of_activity',
        'days_credited',
        'vacation_service_dso_number',
        'inclusive_leave_dates',
        'days_with_pay',
        'service_credit_balance',
        'days_without_pay',
        'nature_of_leave',
        'leave_type_id',
        'record_of_leave_dso_number',
        'remarks',
        'period_start',
        'period_end',
        'parse_state',
        'parse_note',
        'import_batch_id',
        'source_row_number',
    ];

    protected function casts(): array
    {
        return [
            'days_credited' => 'decimal:2',
            'days_with_pay' => 'decimal:2',
            'service_credit_balance' => 'decimal:2',
            'days_without_pay' => 'decimal:2',
            'period_start' => 'date',
            'period_end' => 'date',
        ];
    }

    public function employeeProfile(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id', 'user_id');
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }
}
