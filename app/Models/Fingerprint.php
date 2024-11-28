<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fingerprint extends Model
{
    use HasFactory;
    protected $table = 'check_verify';
    protected $fillable = ['user_id', 'type', 'device_ip', 'fingerprint_in', 'last_processed_timestamp'];

    // Define a relationship to User model (assuming each fingerprint is related to one user)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
