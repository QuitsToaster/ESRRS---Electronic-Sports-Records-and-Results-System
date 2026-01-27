<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameBracket extends Model
{
    protected $fillable = ['game_id', 'teams', 'matches'];

    protected $casts = [
        'teams' => 'array',
        'matches' => 'array',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
