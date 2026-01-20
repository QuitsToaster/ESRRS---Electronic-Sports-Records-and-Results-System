<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = ['name','division','province','city','logo'];

    public function results()
    {
        return $this->hasMany(Result::class); // Make sure your results table has school_id
    }
}
