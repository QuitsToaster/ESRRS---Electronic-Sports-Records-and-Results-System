<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

   protected $fillable = [
    'name',
    'sport_id',
    'sport_type_id',
    'start_date',
    'venue',
    'age_group',
    'category',
    'is_ball_game', 
    'gold_division_id',
    'silver_division_id',
    'bronze_division_id',
    'award_date',
];

    protected $casts = [
        'start_date' => 'date',
        'award_date' => 'datetime',
        'is_ball_game'  => 'boolean',
    ];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function schools()
    {
        return $this->belongsToMany(School::class, 'event_school')
                    ->withTimestamps();
    }
    

    public function sportType()
{
    return $this->belongsTo(SportType::class);
}

public function goldWinner()
{
    return $this->belongsTo(Division::class, 'gold_division_id');
}

public function silverWinner()
{
    return $this->belongsTo(Division::class, 'silver_division_id');
}

public function bronzeWinner()
{
    return $this->belongsTo(Division::class, 'bronze_division_id');
}


// app/Models/Event.php

public function divisions()
{
    return $this->belongsToMany(
        \App\Models\Division::class,
        'division_event' // pivot table
    );
}

public function games()
{
    return $this->hasMany(\App\Models\Game::class);
}



}
