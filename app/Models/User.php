<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Subscription;
use App\Models\Group;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmailNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'is_super_admin',
        'email_verified_at'
    ];
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('expires_at', '>', now())
            ->whereNotNull('starts_at')
            ->whereNotNull('expires_at')
            ->latest('expires_at');
    }
    public function groupMembers()
    {
        return $this->hasMany(\App\Models\GroupMember::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function getGroupRole(Group $group): ?GroupRole
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', $this->id)
            ->with('role')
            ->first();

        return $member?->role;
    }

    public function can_in_group(string $permission, Group $group): bool
    {
        $role = $this->getGroupRole($group);
        return $role ? $role->$permission : false;
    }

    public function isSubscribed(): bool
    {
        return $this->activeSubscription()->exists();
    }

    public function hasWhatsapp(): bool
    {
        $sub = $this->activeSubscription()->with('plan')->first();
        return $sub ? $sub->plan->whatsapp : false;
    }

    public function hasDiscord(): bool
    {
        $sub = $this->activeSubscription()->with('plan')->first();
        return $sub ? $sub->plan->discord : false;
    }
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_members')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Send email verification notification using custom notification
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification());
    }
}
