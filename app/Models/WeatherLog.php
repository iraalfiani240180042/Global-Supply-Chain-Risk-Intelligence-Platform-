<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeatherLog extends Model
{
    protected $fillable = [
        'country_id',
        'weather_type_id',
        'temperature',
        'humidity',
        'wind_speed',
        'recorded_at'
    ];

    protected $casts = [
        'recorded_at' => 'datetime'
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function weatherType(): BelongsTo
    {
        return $this->belongsTo(WeatherType::class);
    }
}