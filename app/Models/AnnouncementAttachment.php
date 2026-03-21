<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementAttachment extends Model
{
    protected $fillable = [
        'announcement_id',
        'filename',
        'path',
        'type',
        'mime_type',
        'size',
    ];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}
