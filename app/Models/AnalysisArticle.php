<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalysisArticle extends Model
{
    protected $fillable = [
        'title',
        'country_id',
        'category',
        'risk_level',
        'recommended',
        'status',
        'image',
        'summary',
        'content',
        'published_at'
    ];

    protected $casts = [
        'published_at' => 'date',
        'recommended' => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}