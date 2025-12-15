<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Org;
use App\Models\Plan;
use App\Support\RevenueReporter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RevenueController extends Controller
{
    public function index(Request $request, RevenueReporter $reporter): View
    {
        $startDate = $this->parseDate($request->input('start_date'));
        $endDate = $this->parseDate($request->input('end_date'));

        if ($startDate && $endDate && $endDate->lt($startDate)) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $planSlug = $this->normalizePlanSlug($request->input('plan'));
        $orgId = $request->integer('org_id') ?: null;

        $syncStatus = $reporter->syncStripe($startDate, $endDate);
        $metrics = $reporter->metrics($startDate, $endDate, $planSlug, $orgId);

        $payments = $reporter->payments($startDate, $endDate, $planSlug, $orgId);
        $invoices = $reporter->invoices($startDate, $endDate, $planSlug, $orgId);
        $refunds = $reporter->refunds($startDate, $endDate, $planSlug, $orgId);

        $organizations = Org::orderBy('name')->get(['id', 'name']);
        $plans = Plan::orderBy('name')->get(['id', 'name', 'slug']);

        return view('platform.revenue.index', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'planSlug' => $planSlug,
            'orgId' => $orgId,
            'syncStatus' => $syncStatus,
            'usingStripe' => $reporter->isStripeEnabled(),
            'metrics' => [
                'total_revenue' => $this->formatMoney($metrics['total_revenue_cents']),
                'mrr' => $this->formatMoney($metrics['mrr_cents']),
                'arr' => $this->formatMoney($metrics['arr_cents']),
                'active_subscriptions' => $metrics['active_subscriptions'],
            ],
            'payments' => $payments,
            'invoices' => $invoices,
            'refunds' => $refunds,
            'plans' => $plans,
            'organizations' => $organizations,
        ]);
    }

    protected function parseDate(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function normalizePlanSlug(?string $plan): ?string
    {
        if (! $plan) {
            return null;
        }

        $plan = strtolower((string) $plan);

        return in_array($plan, ['core', 'pro', 'apex'], true) ? $plan : null;
    }

    protected function formatMoney(int $amountCents, string $currency = 'usd'): string
    {
        $upper = strtoupper($currency);

        return $upper.' '.number_format($amountCents / 100, 2);
    }
}
