<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Event;

class AbstractAccount extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'email',
        'event_id',
        'password',
        'title',
        'full_name',
        'username',
        'phone_number',
        'institution',
        'contact_preference',
        'address',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
