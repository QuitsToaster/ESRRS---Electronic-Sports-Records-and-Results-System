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
}
