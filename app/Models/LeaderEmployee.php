<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaderEmployee extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'leader_id',
        'employee_id',
        'off_day',
        'status'
    ];   
}
