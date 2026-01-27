<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'team_a',
        'team_b',
        'score_a',
        'score_b',
        'scores',
        'status',    
        'game_time',
        'venue',
        'live_video_url',
        'round',
        'winner',
    ];

    protected $casts = [
        'game_time' => 'datetime', 
        'scores'    => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // NEW: Relationship to additional teams
    public function teams()
    {
        return $this->hasMany(GameTeam::class);
    }

    // Dynamic status based purely on current time
    public function getCurrentStatusAttribute()
    {
        $now = Carbon::now();

        if ($now->lt($this->game_time)) {
            return 'Upcoming';
        } elseif ($now->gte($this->game_time) && $now->lt($this->game_time->copy()->addHours(2))) {
            return 'Ongoing';
        } else {
            return 'Finished';
        }
    }
}
