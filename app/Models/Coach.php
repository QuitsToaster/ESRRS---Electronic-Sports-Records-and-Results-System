<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\School;
use App\Models\Sport;

class Coach extends Model
{
    protected $fillable = ['name', 'school_id', 'sport_id'];

    public function school() { return $this->belongsTo(School::class); }
    public function sport() { return $this->belongsTo(Sport::class); }
}
