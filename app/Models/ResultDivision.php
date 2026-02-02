<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultDivision extends Model
{
    protected $table = 'results_divisions';

    protected $fillable = [
        'event_id',
        'sport_id',
        'type_id',
        'division',
        'sdo',
        'category',
        'medal',
        'awarded_by',
    ];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function type()
    {
        return $this->belongsTo(SportType::class, 'type_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function awardedBy()
    {
        return $this->belongsTo(User::class, 'awarded_by');
    }

    public function division_model()
{
    // `division` column in results_divisions table stores the SDO name
    return $this->belongsTo(\App\Models\Division::class, 'sdo', 'name');
}

}
