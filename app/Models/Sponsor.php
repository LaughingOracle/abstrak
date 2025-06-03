<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sponsor extends Model
{
    use HasFactory;

    protected $fillable = [
        'pic_name',
        'pic_phone',
        'pic_email'
    ];

    public function eventAccount()
    {
        return $this->belongsTo(eventAccount::class);
    }
}
