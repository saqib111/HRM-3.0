<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        'leader_id',
        'user_id',
        'shift_id',
        'shift_in',
        'shift_out',
        'duty_hours',
        'check-in',
        'check_out',
        'emergency_checkout',
        'status',
        'offday'
    ];
}
