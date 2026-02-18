<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EscalationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'org_id',
        'name',
        'min_risk_level',
        'match_urgent',
        'match_category',
        'auto_mark_urgent',
        'notify_roles',
    ];

    protected $casts = [
        'match_urgent' => 'boolean',
        'auto_mark_urgent' => 'boolean',
        'notify_roles' => 'array',
    ];

    public function org(): BelongsTo
    {
        return $this->belongsTo(Org::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(EscalationEvent::class);
    }
}
