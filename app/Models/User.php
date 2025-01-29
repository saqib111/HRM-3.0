<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'username',
        'email',
        'password',
        'joining_date',
        'confirmation_status',
        'image',
        'company_id',
        'department_id',
        'designation_id',
        'brand',
        'is-allowed_8_offdays',
        'status',
        'role',
        'userpass'
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

    public function userprofile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function assignedLeaveApproval(): HasOne
    {
        return $this->hasOne(AssignedLeaveApprovals::class);
    }

    public function annualleave(): HasOne
    {
        return $this->hasOne(AnnualLeaves::class);
    }
    public function visaInfo()
    {
        return $this->hasOne(VisaInfo::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    // Relationship with Designation
    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function emergencyUser()
    {
        return $this->hasOne(Emergency::class, 'user_id');
    }

    public function dependantUser()
    {
        return $this->hasOne(Dependant::class, 'user_id');
    }

    // Relationship with leave management (one user can have many leaves)
    public function leaveApplications()
    {
        return $this->hasMany(LeaveManagement::class);
    }

    public function approvedLeaves()
    {
        return $this->hasMany(ApprovedLeave::class);
    }
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
        ];
    }
}
