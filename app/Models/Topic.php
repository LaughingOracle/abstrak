<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Event;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
