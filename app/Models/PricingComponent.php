<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingComponent extends Model
{
    protected $fillable = ['key', 'name', 'price', 'description'];
}
