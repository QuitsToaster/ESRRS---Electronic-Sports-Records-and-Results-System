<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Athlete;
use App\Models\Sport;
use App\Models\Event;

class IndividualAwardResult extends Model
{
    protected $fillable = [
        'event_id','sport_id','type_id','division','category','sdo','medal','athlete_id','awarded_by','status','validation_file', 'revision_message'
    ];

    // Relationship to athlete
    public function athlete()
    {
        return $this->belongsTo(Athlete::class, 'athlete_id');
    }

    // Relationship to sport
    public function sport()
    {
        return $this->belongsTo(Sport::class, 'sport_id');
    }

    // Optional: Relationship to event
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }


public function type()
{
    return $this->belongsTo(\App\Models\SportType::class, 'type_id');
}

public function user()
{
    return $this->belongsTo(\App\Models\User::class, 'awarded_by');
}

}
