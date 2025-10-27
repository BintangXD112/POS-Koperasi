<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketAnalysis extends Model
{
    use HasFactory;

    protected $table = 'market_analysis';

    protected $fillable = [
        'analysis_type',
        'data',
        'insights',
        'recommendations',
        'ai_generated',
        'created_by',
        'analysis_date'
    ];

    protected $casts = [
        'data' => 'array',
        'insights' => 'array',
        'recommendations' => 'array',
        'ai_generated' => 'boolean',
        'analysis_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('analysis_type', $type);
    }

    public function scopeAiGenerated($query)
    {
        return $query->where('ai_generated', true);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('analysis_date', '>=', now()->subDays($days));
    }
}
