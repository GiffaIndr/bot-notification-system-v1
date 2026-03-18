<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $fillable = [
        'user_id',
        'payment_id',
        'plan_id',
        'order_id',
        'amount',
        'starts_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'starts_at'  => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
