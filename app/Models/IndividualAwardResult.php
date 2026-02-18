<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndividualAwardResult extends Model
{
    protected $fillable = [
        'event_id','sport_id','type_id','division','category','sdo','medal','athlete_id','awarded_by'
    ];
}
