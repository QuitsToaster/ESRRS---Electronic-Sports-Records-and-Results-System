<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\School;
use Illuminate\Support\Str;
use App\Models\Sport;

class Athlete extends Model
{
    protected $fillable = [
    'last_name',
    'first_name',
    'middle_initial',
    'sex',
    'birthdate',

    'region',
    'level',
    'event',

    'school_name',
    'school_type',
    'school_address',
    'school_division',
    'municipality',
    'school_code',

    'lrn',

    'school_id',
    'sport_id',
    'division',
    'category',
    'status',
    'qr_code',
    'profile_photo',
    'legislative_district',
];


    public function school() { return $this->belongsTo(School::class); }
    public function sport() { return $this->belongsTo(Sport::class); }

    protected static function booted()
{
    static::creating(function ($athlete) {
        if (empty($athlete->qr_code)) {
            $athlete->qr_code = 'eSRRS-' . strtoupper(Str::uuid());
        }
    });
}

public function getFullNameAttribute()
{
    return trim("{$this->last_name}, {$this->first_name} {$this->middle_initial}");
}

public function attendances()
{
    return $this->hasMany(\App\Models\Attendance::class, 'athlete_id');
}

}

