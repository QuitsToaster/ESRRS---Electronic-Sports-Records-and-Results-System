<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AwardRequest extends Model
{
    protected $fillable = [
        'event_id',
        'award_type',
        'division',
        'category',
        'sdo',
        'payload',
        'attachment',
        'status',
        'submitted_by',
        'validated_by',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    // Relationships
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
