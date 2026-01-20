<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Announcement;

class AnnouncementFile extends Model
{
    protected $fillable = [
        'announcement_id',
        'file_path',
        'file_type'
    ];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }
}
