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
        'status',    // you can keep this for manual override if needed
        'game_time',
        'venue',
    ];

    protected $casts = [
        'game_time' => 'datetime', // ensures Carbon object
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
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
