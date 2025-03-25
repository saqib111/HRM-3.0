<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnlockLeaveCategories extends Model
{
    use HasFactory;

    //
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'leave_details',
        'leave_balance',
        'images',
        'status',
        'superadmin_id',
        'superadmin_created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class, 'user_id');
    }
}
