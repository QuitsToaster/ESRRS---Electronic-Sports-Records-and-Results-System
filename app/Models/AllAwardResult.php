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
}
