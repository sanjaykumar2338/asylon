<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingInvoice extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'org_id',
        'plan_slug',
        'stripe_invoice_id',
        'status',
        'amount',
        'currency',
        'period_start',
        'period_end',
        'paid_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function org(): BelongsTo
    {
        return $this->belongsTo(Org::class);
    }
}
