<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Model; // Make sure this is imported

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

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
        'position',
        'date_employed',
        'sex',
        'date_of_birth',
        'place_of_birth',
        'employee_number',
        'personnel',
        'station',
        'civil_status',
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
            'date_employed' => 'date',
            'date_of_birth' => 'date',
        ];
    }
    public function isAdmin()
    {
        return $this->admin;  // Assumes 'is_admin' field exists
    }
    public function Pending()
    {
        return $this->pending;  // Assumes 'is_admin' field exists
    }
}