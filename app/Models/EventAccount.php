<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'full_name',
        'nik_password',
        'institution',
        'email',
        'phone_number',
        'address',
        'province_country'
    ];
}
