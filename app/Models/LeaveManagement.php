<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveManagement extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'leave_management';

    // Allow mass assignment for these fields
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'leave_details',
        'leave_balance',
        'status_1',
        'team_leader_ids',
        'status_2',
        'manager_ids',
        'first_approval_id',
        'first_approval_created_time',
        'second_approval_id',
        'second_approval_created_time',
        'hr_approval_id',
        'hr_approval_created_time'
    ];

    // Relationship with the User model (Employee)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Team Leaders (assuming multiple team leaders)
    public function teamLeaders()
    {
        return $this->belongsToMany(User::class, 'team_leader_ids');
    }

    // Relationship with Managers (assuming multiple managers)
    public function managers()
    {
        return $this->belongsToMany(User::class, 'manager_ids');
    }

    // Relationship with HR for approval
    public function hr()
    {
        return $this->belongsTo(User::class, 'hr_approval_id');
    }
}