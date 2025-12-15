<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingSubscription extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'org_id',
        'plan_slug',
        'stripe_subscription_id',
        'status',
        'amount',
        'currency',
        'interval',
        'interval_count',
        'cancel_at_period_end',
        'current_period_start',
        'current_period_end',
        'ended_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'cancel_at_period_end' => 'boolean',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function org(): BelongsTo
    {
        return $this->belongsTo(Org::class);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'trialing']);
    }
}
