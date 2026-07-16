<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyLog extends Model
{
    protected $fillable = [
        'country_id',
        'exchange_rate',
        'recorded_at'
    ];

    protected $casts = [
        'recorded_at' => 'datetime'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}