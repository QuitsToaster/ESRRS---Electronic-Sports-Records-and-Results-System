<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\School;
use App\Models\Sport;
use App\Models\Division;

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
        'school_code',

        'lrn',

        'school_id',
        'sport_id',
        'division_id',   // ✅ FK
        'category',
        'status',
        'qr_code',
        'profile_photo',
    ];

    /* =========================
     | Relationships
     ========================= */

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    /* =========================
     | Model Events
     ========================= */

    protected static function booted()
    {
        static::creating(function ($athlete) {
            if (empty($athlete->qr_code)) {
                $athlete->qr_code = 'CEMS-' . strtoupper(Str::uuid());
            }
        });
    }

    /* =========================
     | Accessors
     ========================= */

    public function getFullNameAttribute()
    {
        return trim("{$this->last_name}, {$this->first_name} {$this->middle_initial}");
    }
}
