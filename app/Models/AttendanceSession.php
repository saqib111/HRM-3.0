<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    protected $fillable = [
        'user_id',
        'attendance_id',
        'check_in',
        'check_out',
        'status'
    
    
    ];
}
