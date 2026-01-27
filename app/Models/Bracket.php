<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Event;

class Bracket extends Model
{
    protected $fillable = ['event_id', 'structure'];

    protected $casts = [
        'structure' => 'array', // automatically decode JSON
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}

