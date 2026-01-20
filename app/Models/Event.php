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
        'level',
        'start_date',
        'end_date',
        'venue',
        'age_group',
        'category',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }
}
