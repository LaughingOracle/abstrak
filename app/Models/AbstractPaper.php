<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbstractPaper extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'file_directory',
        'topic',
        'presentation_type',
        'abstract_account_id',
        'presenter_id'
    ];

    public function abstractAccount()
    {
        return $this->belongsTo(abstractAccount::class);
    }

    public function presenter()
    {
        return $this->belongsTo(presenter::class);
    }
    public function author()
    {
        return $this->belongsToMany(author::class);
    }
}
