<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CurrencyMaster extends Model
{
    protected $table = 'currencies_master';

    protected $fillable = [
        'code',
        'name',
        'symbol',
    ];

    public function currencies(): HasMany
    {
        return $this->hasMany(Currency::class);
    }
}