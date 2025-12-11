<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Stripe\StripeClient;

class BillingController extends Controller
{
    public function choosePlan(Request $request): View
    {
        $org = $request->user()->org;
        $plans = Plan::whereIn('slug', ['core', 'pro'])->with('prices')->get();

        return view('billing.choose-plan', compact('org', 'plans'));
    }

    public function createCheckout(Request $request): RedirectResponse
    {
        $org = $request->user()->org;

        if (! $org && ! $request->user()->hasRole('platform_admin')) {
            return back()->with('error', __('No organization found for this user.'));
        }

        $data = $request->validate([
            'plan_slug' => ['required', Rule::exists('plans', 'slug')],
            'interval' => ['sometimes', 'in:monthly,yearly,custom'],
        ]);

        $plan = Plan::where('slug', $data['plan_slug'])->firstOrFail();
        $interval = $data['interval'] ?? 'monthly';

        $stripePriceId = $plan->currentStripePriceId($interval);

        if (! $stripePriceId) {
            return back()->with('error', __('No Stripe price configured for this plan/interval.'));
        }

        if (! class_exists(StripeClient::class) || ! config('services.stripe.secret')) {
            return back()->with('error', __('Stripe is not configured yet. Please set STRIPE_SECRET.'));
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        $metadata = [
            'org_id' => $org?->id,
            'plan_slug' => $plan->slug,
            'interval' => $interval,
        ];

        // Build params without empty customer.
        $params = [
            'mode' => 'subscription',
            'client_reference_id' => $org?->id,
            'metadata' => $metadata,
            'subscription_data' => [
                'metadata' => $metadata,
            ],
            'line_items' => [
                [
                    'price' => $stripePriceId,
                    'quantity' => 1,
                ],
            ],
            'success_url' => route('billing.success'),
            'cancel_url' => route('billing.cancel'),
        ];

        if ($org?->stripe_customer_id) {
            $params['customer'] = $org->stripe_customer_id;
        }

        try {
            $session = $stripe->checkout->sessions->create($params);
        } catch (\Throwable $e) {
            return back()->with('error', __('Unable to start checkout: :message', ['message' => $e->getMessage()]));
        }

        return redirect()->away($session->url);
    }

    public function success(): View
    {
        return view('billing.success');
    }

    public function cancel(): View
    {
        return view('billing.cancel');
    }

    public function settings(Request $request): View
    {
        $org = $request->user()->org;
        $plan = $org?->plan;

        return view('billing.settings', compact('org', 'plan'));
    }

    public function createPortalSession(Request $request): RedirectResponse
    {
        $org = $request->user()->org;

        if (! $org?->stripe_customer_id) {
            abort(400, 'No Stripe customer for this organization yet.');
        }

        if (! class_exists(StripeClient::class) || ! config('services.stripe.secret')) {
            return back()->with('error', __('Stripe is not configured yet. Please set STRIPE_SECRET.'));
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        $session = $stripe->billingPortal->sessions->create([
            'customer' => $org->stripe_customer_id,
            'return_url' => route('billing.settings'),
        ]);

        return redirect()->away($session->url);
    }
}
