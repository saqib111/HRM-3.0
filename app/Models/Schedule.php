<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'start_to',
        'start_end',
        'start_time',
        'end_to',
        'end_end',
        'end_time',
        'name',
        'user_id',
        'status'
    ];

}
