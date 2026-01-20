<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AnnouncementFile;
use App\Models\Event;
use App\Models\User;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'type',
        'content',
        'event_id',
        'user_id',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function files()
    {
        return $this->hasMany(AnnouncementFile::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

