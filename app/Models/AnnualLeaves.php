<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnualLeaves extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'leave_type', 'leave_balance', 'last_year_balance'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}