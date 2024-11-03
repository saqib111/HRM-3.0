<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emergency extends Model
{
    use HasFactory;
    protected $fillable = [
        'e_name',
        'e_phone',
        'e_email',
        'e_address',
        'e_country',
        'e_gender',
        'e_relationship',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}