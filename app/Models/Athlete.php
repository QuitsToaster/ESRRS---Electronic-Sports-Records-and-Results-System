<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\School;
use Illuminate\Support\Str;
use App\Models\Sport;

class Athlete extends Model
{
    protected $fillable = ['name', 'school_id', 'sport_id', 'division', 'category', 'status', 'qr_code',];

    public function school() { return $this->belongsTo(School::class); }
    public function sport() { return $this->belongsTo(Sport::class); }

    protected static function booted()
{
    static::creating(function ($athlete) {
        if (empty($athlete->qr_code)) {
            $athlete->qr_code = 'ATH-' . strtoupper(Str::uuid());
        }
    });
}
}

