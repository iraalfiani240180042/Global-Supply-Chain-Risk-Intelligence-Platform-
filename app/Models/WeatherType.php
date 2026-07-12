<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherType extends Model
{
    protected $fillable = [
        'name'
    ];

    public function weatherLogs()
    {
        return $this->hasMany(WeatherLog::class);
    }
}