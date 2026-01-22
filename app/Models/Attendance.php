<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['athlete_id', 'time_in', 'time_out'];

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    
}
