<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_id',
        'user_id',
        'title',
        'content',
        'scheduled_at',
        'repeat',
        'use_picker',
        'picker_mode',
        'pick_count',
        'pick_role_id',
        'custom_pick_list',
        'picked_result',
    ];

    protected $casts = [
        'scheduled_at'     => 'datetime',
        'use_picker'       => 'boolean',
        'custom_pick_list' => 'array',
        'picked_result'    => 'array',
    ];

    public function pickRole()
    {
        return $this->belongsTo(GroupRole::class, 'pick_role_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
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
