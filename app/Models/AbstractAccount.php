<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbstractAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'password',
        'title',
        'first_name',
        'last_name',
        'phone_number',
    ];
}
