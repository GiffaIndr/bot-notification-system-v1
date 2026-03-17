<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupRole extends Model
{
    protected $fillable = [
        'group_id',
        'name',
        'color',
        'can_create_announcement',
        'can_edit_announcement',
        'can_manage_member',
        'can_generate_code',
        'can_manage_bot',
        'is_owner',
    ];

    protected $casts = [
        'can_create_announcement' => 'boolean',
        'can_edit_announcement' => 'boolean',
        'can_manage_member' => 'boolean',
        'can_generate_code' => 'boolean',
        'can_manage_bot' => 'boolean',
        'is_owner'  => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }
}
