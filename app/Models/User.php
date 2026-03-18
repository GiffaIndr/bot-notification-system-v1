<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Subscription;
use App\Models\Group;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
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
        'phone'
    ];
    public function activeSubscription()
    {
        return $this->hasOne(Payment::class)
            ->where('status', 'success')
            ->where('expires_at', '>', now())
            ->latest();
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
}
