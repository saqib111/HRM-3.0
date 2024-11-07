<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovedLeave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type',
        'leave_sub_type',
        'date',
        'start_time',
        'end_time',
    ];

    /**
     * Get the user that owns the approved leave.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
