<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\AbstractPaper;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'affiliation'
    ];

    public function abstractPaper()
    {
        return $this->belongsToMany(abstractPaper::class);
    }
}
