<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class Group extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function getRouteKey(): mixed
    {
        $encrypted = Crypt::encryptString((string) $this->getKey());

        return rtrim(strtr(base64_encode($encrypted), '+/', '-_'), '=');
    }

    public function resolveRouteBinding($value, $field = null): ?Model
    {
        // Backward-compatible: still accept numeric IDs from old links.
        if (is_numeric($value)) {
            return $this->where($field ?? $this->getRouteKeyName(), $value)->first();
        }

        try {
            $decoded = base64_decode(strtr((string) $value, '-_', '+/'), true);
            if ($decoded === false) {
                return null;
            }

            $id = Crypt::decryptString($decoded);

            return $this->where($field ?? $this->getRouteKeyName(), $id)->first();
        } catch (Throwable) {
            return null;
        }
    }

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

    public function announcementCategories()
    {
        return $this->hasMany(GroupAnnouncementCategory::class);
    }

    public function bots()
    {
        return $this->hasMany(GroupBot::class);
    }

    public function subscription()
    {
        return $this->hasOne(GroupSubscription::class);
    }
}
