<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_id',
        'category_id',
        'user_id',
        'title',
        'content',
        'scheduled_at',
        'deadline_mode',
        'deadline_at',
        'reminder_enabled',
        'reminder_offset_value',
        'reminder_offset_unit',
        'reminder_at',
        'reminder_sent_at',
        'reminder_send_status',
        'status',
        'repeat',
        'use_picker',
        'picker_mode',
        'pick_count',
        'pick_role_id',
        'custom_pick_list',
        'is_pinned',
        'picked_result',
    ];

    protected $casts = [
        'scheduled_at'     => 'datetime',
        'deadline_mode'    => 'boolean',
        'deadline_at'      => 'datetime',
        'reminder_enabled' => 'boolean',
        'reminder_at'      => 'datetime',
        'reminder_sent_at' => 'datetime',
        'use_picker'       => 'boolean',
        'is_pinned' => 'boolean',
        'custom_pick_list' => 'array',
        'picked_result'    => 'array',
    ];
    public function attachments()
    {
        return $this->hasMany(AnnouncementAttachment::class);
    }

    public function pickRole()
    {
        return $this->belongsTo(GroupRole::class, 'pick_role_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function category()
    {
        return $this->belongsTo(GroupAnnouncementCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function reactions()
    {
        return $this->hasMany(AnnouncementReaction::class);
    }
}
