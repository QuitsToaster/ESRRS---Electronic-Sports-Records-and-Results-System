<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SportType;

class AllAwardResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id','sport_id','type_id','division','category','sdo','medal','count','awarded_by','status','validation_file', 'revision_message'
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

public function sportType()
{
    return $this->belongsTo(\App\Models\SportType::class, 'type_id');
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
