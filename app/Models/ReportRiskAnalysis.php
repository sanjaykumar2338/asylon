<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportRiskAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'risk_score',
        'risk_level',
        'matched_keywords',
        'signals',
    ];

    protected $casts = [
        'matched_keywords' => 'array',
        'signals' => 'array',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
