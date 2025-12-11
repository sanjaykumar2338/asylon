<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'max_users',
        'max_reports_per_month',
        'trial_days',
        'is_active',
    ];

    public function prices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PlanPrice::class);
    }

    public function priceFor(string $interval, bool $earlyAdopter = false): ?PlanPrice
    {
        return $this->prices
            ->where('billing_interval', $interval)
            ->where('is_early_adopter', $earlyAdopter)
            ->where('is_active', true)
            ->first();
    }

    public function currentStripePriceId(string $interval): ?string
    {
        $early = (bool) config('asylon.early_adopter_mode', true);

        $price = $this->priceFor($interval, $early) ?: $this->priceFor($interval, false);

        return $price?->stripe_price_id;
    }
}
