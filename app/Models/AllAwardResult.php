<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllAwardResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id','sport_id','type_id','division','category','sdo','medal','count','awarded_by'
    ];

    // -----------------------------
    // RELATIONSHIPS
    // -----------------------------
    public function sport()
    {
        return $this->belongsTo(Sport::class, 'sport_id');
    }

    public function event()
    {
        return $this->belongsTo(\App\Models\Event::class, 'event_id');
    }

    public function type()
    {
        return $this->belongsTo(\App\Models\SportType::class, 'type_id');
    }
}
