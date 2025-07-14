<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Event;

class EventForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'label',
        'type',
        'html',
        'score_config'
    ];

    public function Event()
    {
        return $this->belongsTo(event::class);
    }
}
