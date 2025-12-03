<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreatAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'score',
        'level',
        'summary',
        'signals',
        'recommendation',
        'subject_of_concern',
    ];

    protected $casts = [
        'signals' => 'array',
        'subject_of_concern' => 'boolean',
        'score' => 'integer',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
