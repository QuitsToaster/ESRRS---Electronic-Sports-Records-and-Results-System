<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SportType extends Model
{
    protected $table = 'sport_types'; // ✅ FORCE TABLE

    protected $fillable = [
        'sport_id',
        'type_name',
    ];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }
}

