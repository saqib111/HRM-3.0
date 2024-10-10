<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [

        'first_name',
        'last_name',
        'email',

        'employee_id',
        'phone',
        'joining_date',
        'company',
        'department',
        'position',
        'passport_no',
        'role',
        'visa_issue',
        'visa_expire',
        'img',

    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function UsersData()
    {
        return $this->hasMany('App\Models\Designation');
    }
}