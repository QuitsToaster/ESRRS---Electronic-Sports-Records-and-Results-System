<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type_of_game', 'division',
    'category', 'type',];


    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function types()
{
    return $this->hasMany(SportType::class);
}

public function sportTypes()
{
    return $this->hasMany(SportType::class);
}


}
