<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'real_name',
        'dob',
        'accomodation',
        'gender',
        'phone',
        'nationality',
        'religion',
        'telegram',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function UsersData()
    {
        return $this->hasMany('App\Models\Designation');
    }
}