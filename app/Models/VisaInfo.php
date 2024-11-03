<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaInfo extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', // Include user_id in fillable
        'passport_no', // Include passport_no in fillable
        'p_issue_date',
        'p_expiry_date',
        'visa_no',
        'v_issue_date',
        'v_expiry_date',
        'foreign_no',
        'f_expiry_date',

    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}