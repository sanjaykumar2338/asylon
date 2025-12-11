<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanPrice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        $plans = Plan::with(['prices' => function ($query): void {
            $query->orderByRaw("FIELD(billing_interval, 'monthly','yearly','custom')")
                ->orderBy('is_early_adopter');
        }])->withCount('prices')->orderBy('name')->get();

        return view('platform.plans.index', [
            'plans' => $plans,
        ]);
    }

    public function editPrices(Plan $plan): View
    {
        $plan->load(['prices' => function ($query): void {
            $query->orderByRaw("FIELD(billing_interval, 'monthly','yearly','custom')")
                ->orderBy('is_early_adopter');
        }]);

        $intervals = [
            'monthly' => 'Monthly',
            'yearly' => 'Yearly',
            'custom' => 'Custom',
        ];

        return view('platform.plans.prices', [
            'plan' => $plan,
            'intervals' => $intervals,
        ]);
    }

    public function updatePrices(Request $request, Plan $plan): RedirectResponse
    {
        $plan->load('prices');
        $existingIds = $plan->prices->pluck('id')->all();

        $rules = [
            'prices' => ['required', 'array'],
            'new_price.is_early_adopter' => ['sometimes', 'boolean'],
            'new_price.is_active' => ['sometimes', 'boolean'],
        ];

        foreach ($existingIds as $priceId) {
            $rules["prices.$priceId.stripe_price_id"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('plan_prices', 'stripe_price_id')->ignore($priceId),
            ];
            $rules["prices.$priceId.is_active"] = ['sometimes', 'boolean'];
        }

        $rules['new_price.billing_interval'] = ['nullable', Rule::in(['monthly', 'yearly', 'custom'])];
        $rules['new_price.stripe_price_id'] = [
            'nullable',
            'string',
            'max:255',
            Rule::unique('plan_prices', 'stripe_price_id'),
        ];

        $request->validate($rules);

        foreach ($request->input('prices', []) as $priceId => $payload) {
            if (! in_array((int) $priceId, $existingIds, true)) {
                continue;
            }

            $plan->prices()->whereKey($priceId)->update([
                'stripe_price_id' => $payload['stripe_price_id'],
                'is_active' => (bool) ($payload['is_active'] ?? false),
            ]);
        }

        $newPrice = $request->input('new_price', []);
        $newStripeId = $newPrice['stripe_price_id'] ?? null;
        $newInterval = $newPrice['billing_interval'] ?? null;
        $newEarly = (bool) ($newPrice['is_early_adopter'] ?? false);
        $newActive = (bool) ($newPrice['is_active'] ?? false);

        if ($newStripeId && $newInterval) {
            $exists = $plan->prices()
                ->where('billing_interval', $newInterval)
                ->where('is_early_adopter', $newEarly)
                ->exists();

            if ($exists) {
                return back()
                    ->withErrors(['new_price.billing_interval' => __('A price already exists for this interval/early adopter combination.')])
                    ->withInput();
            }

            PlanPrice::create([
                'plan_id' => $plan->id,
                'billing_interval' => $newInterval,
                'is_early_adopter' => $newEarly,
                'stripe_price_id' => $newStripeId,
                'is_active' => $newActive,
            ]);
        }

        return back()->with('ok', __('Plan prices updated.'));
    }
}
