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
            ->withPivot('role');
    }

    public function invitations()
    {
        return $this->hasMany(InvitationCode::class);
    }
    public function announcements()
    {
        return $this->hasMany(Announcement::class)->latest();
    }

    public function bots()
    {
        return $this->hasMany(GroupBot::class);
    }
}
