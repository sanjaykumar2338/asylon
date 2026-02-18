<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingRefund extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'org_id',
        'plan_slug',
        'stripe_refund_id',
        'stripe_charge_id',
        'amount',
        'currency',
        'reason',
        'refunded_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'refunded_at' => 'datetime',
    ];

    public function org(): BelongsTo
    {
        return $this->belongsTo(Org::class);
    }
}
