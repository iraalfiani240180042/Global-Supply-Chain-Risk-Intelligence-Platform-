<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeatherType extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];

    public function weatherLogs(): HasMany
    {
        return $this->hasMany(WeatherLog::class);
    }
}