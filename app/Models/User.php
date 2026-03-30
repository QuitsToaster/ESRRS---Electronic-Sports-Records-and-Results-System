<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\AllAwardResult;
use App\Models\IndividualAwardResult;
use App\Models\Role;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'access',
        'sports_access',
        'sport_type_access', 
        'level_access',      // ✅ FIX
        'category_access', 
        'division_name',
        'divisions', 
        'contact_number',  // ✅ FIX
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'access'          => 'array',
        'sports_access'   => 'array',
        'sport_type_access'  => 'array',
        'level_access'    => 'array',   // ✅ FIX
        'category_access' => 'array',  
        'divisions'       => 'array',  // ✅ FIX
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function allAwards()
{
    return $this->hasMany(AllAwardResult::class, 'awarded_by');
}

public function individualAwards()
{
    return $this->hasMany(IndividualAwardResult::class, 'awarded_by');
}
}
