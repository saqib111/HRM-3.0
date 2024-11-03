<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignedLeaveApprovals extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'first_assigned_user_id', 'second_assigned_user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}