<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Currency extends Model
{
    protected $fillable = [
        'country_id',
        'currency_master_id',
        'exchange_rate',
        'updated_at_api',
    ];

    protected $casts = [
        'updated_at_api' => 'datetime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function currencyMaster(): BelongsTo
    {
        return $this->belongsTo(CurrencyMaster::class);
    }
}