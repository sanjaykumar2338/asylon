<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\BillingEvent;
use App\Models\BillingInvoice;
use App\Models\BillingPayment;
use App\Models\Org;
use App\Models\Plan;
use App\Services\StripeSubscriptionManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeSuperAdmin();

        $search = (string) $request->query('search', '');
        $status = (string) $request->query('status', '');
        $planSlug = (string) $request->query('plan', '');
        $renewalStart = $request->date('renewal_start');
        $renewalEnd = $request->date('renewal_end');

        $orgs = Org::query()
            ->with(['plan', 'latestBillingSubscription'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('contact_email', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn ($q) => $q->where('billing_status', $status))
            ->when($planSlug !== '', fn ($q) => $q->whereHas('plan', fn ($p) => $p->where('slug', $planSlug)))
            ->when($renewalStart || $renewalEnd, function ($q) use ($renewalStart, $renewalEnd): void {
                $q->whereHas('latestBillingSubscription', function ($sub) use ($renewalStart, $renewalEnd): void {
                    if ($renewalStart) {
                        $sub->where('current_period_end', '>=', $renewalStart->startOfDay());
                    }
                    if ($renewalEnd) {
                        $sub->where('current_period_end', '<=', $renewalEnd->endOfDay());
                    }
                });
            })
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        $plans = Plan::orderBy('name')->get(['id', 'name', 'slug']);

        return view('platform.subscriptions.index', [
            'orgs' => $orgs,
            'plans' => $plans,
            'search' => $search,
            'status' => $status,
            'planSlug' => $planSlug,
            'renewalStart' => $renewalStart,
            'renewalEnd' => $renewalEnd,
        ]);
    }

    public function show(Org $org): View
    {
        $this->authorizeSuperAdmin();

        $org->load(['plan', 'latestBillingSubscription']);
        $subscriptions = $org->billingSubscriptions()->latest('current_period_end')->limit(5)->get();
        $invoices = BillingInvoice::where('org_id', $org->id)->latest('paid_at')->limit(10)->get();
        $payments = BillingPayment::where('org_id', $org->id)->latest('paid_at')->limit(10)->get();
        $events = BillingEvent::where('org_id', $org->id)->latest()->limit(20)->get();
        $plans = Plan::with('prices')->orderBy('name')->get();

        return view('platform.subscriptions.show', [
            'org' => $org,
            'subscriptions' => $subscriptions,
            'invoices' => $invoices,
            'payments' => $payments,
            'events' => $events,
            'plans' => $plans,
        ]);
    }

    public function changePlan(Request $request, Org $org, StripeSubscriptionManager $manager): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $data = $request->validate([
            'plan_slug' => ['required', Rule::in(['core', 'pro', 'apex'])],
            'interval' => ['nullable', Rule::in(['monthly', 'yearly'])],
        ]);

        if ($data['plan_slug'] === 'apex') {
            return back()->with('error', __('APEX is custom. Please contact sales to adjust this plan.'));
        }

        $plan = Plan::where('slug', $data['plan_slug'])->firstOrFail();
        $interval = $data['interval'] ?? 'monthly';
        $priceId = $plan->currentStripePriceId($interval);

        if (! $priceId) {
            return back()->with('error', __('No Stripe price configured for this plan/interval.'));
        }

        $result = $manager->changePlan($org, $priceId, $plan->slug, $request->user()->id);

        return back()->with($result['ok'] ? 'ok' : 'error', $result['message']);
    }

    public function overrideStatus(Request $request, Org $org, StripeSubscriptionManager $manager): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $data = $request->validate([
            'status' => ['required', Rule::in(['active', 'past_due', 'canceled', 'pending'])],
        ]);

        $result = $manager->setStatusOverride($org, $data['status'], $request->user()->id);

        return back()->with($result['ok'] ? 'ok' : 'error', $result['message']);
    }

    public function sync(Request $request, Org $org, StripeSubscriptionManager $manager): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $result = $manager->syncOrg($org);

        return back()->with($result['ok'] ? 'ok' : 'error', $result['message']);
    }

    public function cancel(Request $request, Org $org, StripeSubscriptionManager $manager): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $result = $manager->cancelAtPeriodEnd($org, $request->user()->id);

        return back()->with($result['ok'] ? 'ok' : 'error', $result['message']);
    }

    public function resume(Request $request, Org $org, StripeSubscriptionManager $manager): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $result = $manager->resume($org, $request->user()->id);

        return back()->with($result['ok'] ? 'ok' : 'error', $result['message']);
    }

    protected function authorizeSuperAdmin(): void
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);
    }
}
