<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'region_id',
        'name',
        'iso_code',
        'capital',
        'population',
        'latitude',
        'longitude',
        'flag',

        // Currency
        'currency_name',
        'currency_code',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function weatherLogs(): HasMany
    {
        return $this->hasMany(WeatherLog::class);
    }

    public function currencies(): HasMany
    {
        return $this->hasMany(Currency::class);
    }

    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }

    public function ports(): HasMany
    {
        return $this->hasMany(Port::class);
    }

    public function riskScores(): HasMany
    {
        return $this->hasMany(RiskScore::class);
    }
}