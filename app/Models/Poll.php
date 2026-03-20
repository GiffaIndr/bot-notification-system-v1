<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    protected $fillable = [
        'group_id',
        'user_id',
        'question',
        'type',
        'is_anonymous',
        'is_closed',
        'closes_at'
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_closed'    => 'boolean',
        'closes_at'    => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function options()
    {
        return $this->hasMany(PollOption::class);
    }
    public function votes()
    {
        return $this->hasMany(PollVote::class);
    }

    public function userVote()
    {
        return $this->hasOne(PollVote::class)->where('user_id', auth()->id());
    }

    public function isExpired(): bool
    {
        return $this->closes_at && $this->closes_at->isPast();
    }
}
