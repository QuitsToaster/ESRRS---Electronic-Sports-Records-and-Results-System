<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\School;
use App\Models\Sport;

class Coach extends Model
{
    protected $fillable = [
        'name',
        'school_id',
        'sport_id',
        'profile_photo',
        'qr_code',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function getFullNameAttribute()
{
    return trim("{$this->last_name}, {$this->first_name} {$this->middle_initial}");
}

}
