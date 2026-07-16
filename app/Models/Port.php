<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Port extends Model
{
    protected $fillable = [
        'country_id',
        'status_id',
        'name',
        'city',
        'latitude',
        'longitude',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(PortStatus::class);
    }
}