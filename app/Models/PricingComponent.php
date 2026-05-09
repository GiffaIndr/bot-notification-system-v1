<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class PricingComponent extends Model
{
    protected $fillable = ['key', 'name', 'price', 'description'];

    public const CACHE_KEY = 'pricing_components.cached_prices';

    public static function cachedPrices(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addHour(), function () {
            return self::query()->pluck('price', 'key')->all();
        });
    }

    public static function flushCachedPrices(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected static function booted(): void
    {
        static::saved(fn() => self::flushCachedPrices());
        static::deleted(fn() => self::flushCachedPrices());
    }
}
