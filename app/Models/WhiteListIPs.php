<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhiteListIPs extends Model
{
    //
    use HasFactory;

    protected $table = "whitelist_ips";

    protected $fillable = [
        "name",
        "ip_address",
    ];
}