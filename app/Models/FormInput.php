<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use App\Models\EventForm;
use App\Models\AbstractPaper;

class FormInput extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_form_id',
        'abstract_paper_id',
        'value'
    ];

    public function eventForm()
    {
        return $this->belongsTo(eventForm::class);
    }

    public function abstractPaper()
    {
        return $this->belongsTo(abstractPaper::class);
    }
}
