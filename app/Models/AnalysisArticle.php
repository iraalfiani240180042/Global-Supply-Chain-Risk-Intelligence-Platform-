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
        'summary',
        'content',
        'published_at'
    ];

    protected $casts = [
        'published_at'=>'date'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}