<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    // Allow mass assignment for these fields
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_value',
        'new_value',
    ];

    // Optional: define relationship to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
