<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AbstractAccount extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'email',
        'password',
        'title',
        'full_name',
        'username',
        'phone_number',
        'institution',
        'contact_preference',
        'address',
        ];
}
