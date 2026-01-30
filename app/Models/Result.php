<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{

    protected $fillable = [
        'school_id',
        'athlete_id',
        'event_id',
        'type_id', 
        'sport_id',
        'division',
        'category',
        'team_name',
        'medal',
        'awarded_by',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function awardedBy()
    {
        return $this->belongsTo(User::class, 'awarded_by');
    }
    
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    // Result.php
public function division_model()
{
    return $this->belongsTo(\App\Models\Division::class, 'division', 'name'); 
    // Assuming division in results table stores the division name
}

}
