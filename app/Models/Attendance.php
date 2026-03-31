<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'coach_id',
        'time_in',
        'time_out',
        'location',
    ];

    // ADD THESE CASTS - THIS IS CRITICAL!
    protected $casts = [
        'time_in' => 'datetime',
        'time_out' => 'datetime',
    ];

    // Athlete relationship
    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    // Coach relationship
    public function coach()
    {
        return $this->belongsTo(Coach::class);
    }
}