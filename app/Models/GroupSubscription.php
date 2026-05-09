<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'max_members',
        'active_bots_count',
        'max_bots',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function isActive(): bool
    {
        return $this->expires_at && $this->expires_at->isFuture();
    }

    public function daysRemaining(): int
    {
        if (!$this->expires_at) return 0;
        return (int) $this->expires_at->diffInDays(now());
    }
}
