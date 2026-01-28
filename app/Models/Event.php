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
    'gold_school_id',
    'silver_school_id',
    'bronze_school_id',
];

    protected $casts = [
        'start_date' => 'date',
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
    return $this->belongsTo(School::class, 'gold_school_id');
}

public function silverWinner()
{
    return $this->belongsTo(School::class, 'silver_school_id');
}

public function bronzeWinner()
{
    return $this->belongsTo(School::class, 'bronze_school_id');
}

}
