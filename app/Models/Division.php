<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'logo'];

    /**
     * Get all results for this division
     */
    public function results()
    {
        return $this->hasMany(\App\Models\Result::class, 'division', 'name');
        // 'division' column in results table matches 'name' in divisions table
    }

    // Division.php
public function athletes()
{
    return $this->hasManyThrough(
        \App\Models\Athlete::class,
        \App\Models\Result::class,
        'division', // results.division (matches divisions.name)
        'id',       // athletes.id
        'name',     // divisions.name
        'athlete_id' // results.athlete_id
    );
}

public function resultsDivisions()
{
    return $this->hasMany(\App\Models\ResultDivision::class, 'sdo', 'name');
}

// Results in AllAwardResult table
    public function allAwardResults()
    {
        return $this->hasMany(\App\Models\AllAwardResult::class, 'division', 'name');
        // 'division' column in all_award_results matches 'name' in divisions table
    }

    // Results in IndividualAwardResult table
    public function individualAwardResults()
    {
        return $this->hasMany(\App\Models\IndividualAwardResult::class, 'division', 'name');
        // 'division' column in individual_award_results matches 'name' in divisions table
    }

}
