<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    public function groupMembers()
    {
        return $this->hasMany(GroupMember::class);
    }
    public function polls()
    {
        return $this->hasMany(Poll::class)->latest();
    }

    public function roles()
    {
        return $this->hasMany(GroupRole::class);
    }

    public function ownerRole()
    {
        return $this->roles()->where('is_owner', true)->first();
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class)->latest();
    }

    public function invitations()
    {
        return $this->hasMany(InvitationCode::class);
    }
    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function bots()
    {
        return $this->hasMany(GroupBot::class);
    }
}
