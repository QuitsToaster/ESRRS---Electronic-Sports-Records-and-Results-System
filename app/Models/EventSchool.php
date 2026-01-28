<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSchool extends Model
{
    use HasFactory;

    protected $table = 'event_school';

    protected $fillable = [
        'event_id',
        'school_id',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
