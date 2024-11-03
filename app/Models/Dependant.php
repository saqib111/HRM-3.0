<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dependant extends Model
{
    use HasFactory;
    protected $fillable = [
        'd_name',
        'd_gender',
        'd_nationality',
        'd_dob',
        'd_passport_no',
        'd_pass_issue_date',
        'd_pass_expiry_date',
        'd_visa_no',
        'd_visa_issue_date',
        'd_visa_expiry_date',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}