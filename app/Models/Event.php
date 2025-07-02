<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\EventForm;
use App\Models\Topics;
use App\Models\AbstractPaper;
use App\Models\abstractAccount;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_name'
    ];

    public function eventForm() {
        return $this->hasMany(eventForm::class);
    }

    public function topic() {
        return $this->hasMany(topic::class);
    }

    public function abstractPaper()
    {
        return $this->hasMany(abstractPaper::class);
    }

        public function abstractAccount()
    {
        return $this->hasMany(AbstractAccount::class);
    }
}
