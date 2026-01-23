<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemDeveloper extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'contact', 'role', 'description', 'photo',
    ];
}
