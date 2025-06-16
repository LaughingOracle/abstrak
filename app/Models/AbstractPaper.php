<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Author;
use App\Models\Presenter;
use App\Models\AbstractAccount;
use App\Models\Event;

class AbstractPaper extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'title',
        'description',
        'reviewer',
        'topic',
        'presentation_type',
        'abstract_account_id',
        'presenter_id',
        'status'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function abstractAccount()
    {
        return $this->belongsTo(AbstractAccount::class);
    }

    public function presenter()
    {
        return $this->belongsTo(Presenter::class);
    }
    public function author()
    {
        return $this->belongsToMany(Author::class);
    }
}
