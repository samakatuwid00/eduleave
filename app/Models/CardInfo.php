<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardInfo extends Model
{
    use HasFactory;

    protected $table = 'card_info';

    protected $fillable = [
        'id',
        'emp_num',
        'inclusive_period',
        'nature_of_activity',
        'no_of_days_credited',
        'dso_no_vsr',
        'inclusive_dates',
        'no_days_leave',
        'service_cred_bal',
        'leave_without_pay',
        'nature_of_leave',
        'dso_no_rol',
        'remarks',
    ];

    public function user()
    {
        // Link 'emp_num' in 'CardInfo' to 'employee_number' in 'User'
        return $this->belongsTo(User::class, 'emp_num', 'employee_number');
    }
}
