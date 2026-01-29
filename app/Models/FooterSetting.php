<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterSetting extends Model
{
    protected $fillable = ['logo', 'logo_name', 'socials'];

    protected $casts = [
        'socials' => 'array', // JSON to array
    ];
}
