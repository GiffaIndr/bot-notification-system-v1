<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'has_whatsapp',
        'has_discord',
        'has_telegram',
        'max_groups',
        'max_members',
        'total_price',
        'duration_months',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'has_whatsapp' => 'boolean',
        'has_discord'  => 'boolean',
        'has_telegram' => 'boolean',
        'duration_months' => 'integer',
        'starts_at'    => 'datetime',
        'expires_at'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
