<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NonTeachingLeaveCard extends Model
{
    use HasFactory;

    protected $table = 'non_teaching_leave_cards';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'employee_profile_id',
        'period',
        'particulars',
        'leave_type_id',
        'vacation_leave_earned',
        'vacation_leave_with_pay',
        'vacation_leave_balance',
        'vacation_leave_without_pay',
        'sick_leave_earned',
        'sick_leave_with_pay',
        'sick_leave_balance',
        'sick_leave_without_pay',
        'leave_application_action',
        'period_start',
        'period_end',
        'vacation_leave_with_pay_value',
        'vacation_leave_balance_value',
        'sick_leave_balance_value',
        'sick_leave_without_pay_value',
        'application_action_code',
        'parse_state',
        'parse_note',
    ];

    protected function casts(): array
    {
        return [
            'vacation_leave_earned' => 'decimal:2',
            'vacation_leave_without_pay' => 'decimal:2',
            'sick_leave_earned' => 'decimal:2',
            'sick_leave_with_pay' => 'decimal:2',
            'vacation_leave_with_pay_value' => 'decimal:2',
            'vacation_leave_balance_value' => 'decimal:2',
            'sick_leave_balance_value' => 'decimal:2',
            'sick_leave_without_pay_value' => 'decimal:2',
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
