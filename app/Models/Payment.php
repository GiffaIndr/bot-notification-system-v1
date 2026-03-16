<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
    'expires_at' => 'datetime',
    'starts_at'  => 'datetime',
];

    public function user()
{
    return $this->belongsTo(User::class);
}
   public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}

