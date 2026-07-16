<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PortStatus extends Model
{
    protected $fillable = [
        'status',
    ];

    public function ports(): HasMany
    {
        return $this->hasMany(Port::class);
    }
}