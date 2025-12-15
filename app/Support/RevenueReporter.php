<?php

namespace App\Support;

use App\Models\BillingInvoice;
use App\Models\BillingPayment;
use App\Models\BillingRefund;
use App\Models\BillingSubscription;
use App\Models\Org;
use App\Models\PlanPrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class RevenueReporter
{
    protected bool $stripeEnabled = false;

    protected ?StripeClient $stripe = null;

    /**
     * @var array<string, string|null>
     */
    protected array $priceToPlan = [];

    /**
     * @var array<string, int|null>
     */
    protected array $customerToOrg = [];

    public function __construct()
    {
        $this->stripeEnabled = class_exists(StripeClient::class) && (bool) config('services.stripe.secret');

        if ($this->stripeEnabled) {
            $this->stripe = new StripeClient(config('services.stripe.secret'));
        }

        $this->priceToPlan = PlanPrice::with('plan')
            ->get()
            ->mapWithKeys(fn ($price) => [$price->stripe_price_id => $price->plan?->slug])
            ->toArray();
    }

    public function isStripeEnabled(): bool
    {
        return $this->stripeEnabled;
    }

    /**
     * Attempt to refresh local billing data from Stripe.
     *
     * @return array{ok: bool, error: string|null}
     */
    public function syncStripe(?Carbon $startDate, ?Carbon $endDate): array
    {
        if (! $this->stripeEnabled || ! $this->stripe) {
            return ['ok' => false, 'error' => 'Stripe not configured'];
        }

        $errors = [];

        foreach ([
            'invoices' => fn () => $this->syncInvoices($startDate, $endDate),
            'payments' => fn () => $this->syncPayments($startDate, $endDate),
            'refunds' => fn () => $this->syncRefunds($startDate, $endDate),
            'subscriptions' => fn () => $this->syncSubscriptions(),
        ] as $type => $callback) {
            try {
                $callback();
            } catch (\Throwable $e) {
                $errors[] = "{$type}: {$e->getMessage()}";
                Log::warning('Revenue sync from Stripe failed', [
                    'type' => $type,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'ok' => count($errors) === 0,
            'error' => $errors ? implode('; ', $errors) : null,
        ];
    }

    public function metrics(?Carbon $startDate, ?Carbon $endDate, ?string $planSlug, ?int $orgId): array
    {
        $paymentsQuery = BillingPayment::query();
        $this->applyFilters($paymentsQuery, $startDate, $endDate, $planSlug, $orgId, 'paid_at');
        $totalRevenueCents = (int) $paymentsQuery->sum('amount');

        $cacheKey = $this->cacheKey('mrr', $planSlug, $orgId);
        $mrrCents = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($planSlug, $orgId): int {
            $subsQuery = BillingSubscription::active();

            if ($planSlug) {
                $subsQuery->where('plan_slug', $planSlug);
            }

            if ($orgId) {
                $subsQuery->where('org_id', $orgId);
            }

            $total = 0;

            foreach ($subsQuery->get() as $subscription) {
                $total += (int) round($this->normalizeToMonthly(
                    (int) $subscription->amount,
                    (string) ($subscription->interval ?? 'month'),
                    (int) ($subscription->interval_count ?? 1)
                ));
            }

            if ($total === 0) {
                $total = $this->estimateMrrFromInvoices($planSlug, $orgId);
            }

            return $total;
        });

        $arrCents = $mrrCents * 12;

        $activeSubscriptions = BillingSubscription::active()
            ->when($planSlug, fn ($q) => $q->where('plan_slug', $planSlug))
            ->when($orgId, fn ($q) => $q->where('org_id', $orgId))
            ->count();

        if ($activeSubscriptions === 0) {
            $activeSubscriptions = Org::query()
                ->where('billing_status', 'active')
                ->when($planSlug, fn ($q) => $q->whereHas('plan', fn ($p) => $p->where('slug', $planSlug)))
                ->when($orgId, fn ($q) => $q->where('id', $orgId))
                ->count();
        }

        return [
            'total_revenue_cents' => $totalRevenueCents,
            'mrr_cents' => $mrrCents,
            'arr_cents' => $arrCents,
            'active_subscriptions' => $activeSubscriptions,
        ];
    }

    public function payments(?Carbon $startDate, ?Carbon $endDate, ?string $planSlug, ?int $orgId, int $limit = 10)
    {
        return BillingPayment::with('org')
            ->tap(fn ($q) => $this->applyFilters($q, $startDate, $endDate, $planSlug, $orgId, 'paid_at'))
            ->orderByDesc('paid_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function invoices(?Carbon $startDate, ?Carbon $endDate, ?string $planSlug, ?int $orgId, int $limit = 10)
    {
        return BillingInvoice::with('org')
            ->tap(fn ($q) => $this->applyFilters($q, $startDate, $endDate, $planSlug, $orgId, 'paid_at'))
            ->orderByDesc('paid_at')
            ->orderByDesc('period_end')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function refunds(?Carbon $startDate, ?Carbon $endDate, ?string $planSlug, ?int $orgId, int $limit = 10)
    {
        return BillingRefund::with('org')
            ->tap(fn ($q) => $this->applyFilters($q, $startDate, $endDate, $planSlug, $orgId, 'refunded_at'))
            ->orderByDesc('refunded_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    protected function applyFilters($query, ?Carbon $startDate, ?Carbon $endDate, ?string $planSlug, ?int $orgId, string $dateColumn): void
    {
        if ($startDate) {
            $query->where($dateColumn, '>=', $startDate->startOfDay());
        }

        if ($endDate) {
            $query->where($dateColumn, '<=', $endDate->endOfDay());
        }

        if ($planSlug) {
            $query->where('plan_slug', $planSlug);
        }

        if ($orgId) {
            $query->where('org_id', $orgId);
        }
    }

    protected function cacheKey(string $prefix, ?string $planSlug, ?int $orgId): string
    {
        return implode(':', [
            'revenue',
            $prefix,
            $planSlug ?: 'all',
            $orgId ?: 'all',
        ]);
    }

    protected function normalizeToMonthly(int $amountCents, string $interval, int $intervalCount = 1): float
    {
        if ($intervalCount < 1) {
            $intervalCount = 1;
        }

        return match ($interval) {
            'year', 'annual', 'yearly' => $amountCents / 12 / $intervalCount,
            'week', 'weekly' => ($amountCents * 52) / 12 / $intervalCount,
            'day', 'daily' => ($amountCents * 30) / $intervalCount,
            default => $amountCents / $intervalCount, // month or unknown defaults to monthly cadence
        };
    }

    protected function estimateMrrFromInvoices(?string $planSlug, ?int $orgId): int
    {
        $invoiceQuery = BillingInvoice::query()
            ->where('status', 'paid')
            ->where('paid_at', '>=', now()->subDays(35));

        if ($planSlug) {
            $invoiceQuery->where('plan_slug', $planSlug);
        }

        if ($orgId) {
            $invoiceQuery->where('org_id', $orgId);
        }

        return (int) $invoiceQuery->sum('amount');
    }

    protected function syncInvoices(?Carbon $startDate, ?Carbon $endDate): void
    {
        if (! $this->stripe) {
            return;
        }

        $params = ['limit' => 100];
        $created = $this->createdFilter($startDate, $endDate);

        if ($created) {
            $params['created'] = $created;
        }

        $invoices = $this->stripe->invoices->all($params);

        foreach ($invoices->data ?? [] as $invoice) {
            $this->storeInvoicePayload($this->toArray($invoice));
        }
    }

    protected function syncPayments(?Carbon $startDate, ?Carbon $endDate): void
    {
        if (! $this->stripe) {
            return;
        }

        $params = ['limit' => 100];
        $created = $this->createdFilter($startDate, $endDate);

        if ($created) {
            $params['created'] = $created;
        }

        $payments = $this->stripe->paymentIntents->all($params);

        foreach ($payments->data ?? [] as $payment) {
            $this->storePaymentPayload($this->toArray($payment));
        }
    }

    protected function syncRefunds(?Carbon $startDate, ?Carbon $endDate): void
    {
        if (! $this->stripe) {
            return;
        }

        $params = ['limit' => 50];
        $created = $this->createdFilter($startDate, $endDate);

        if ($created) {
            $params['created'] = $created;
        }

        $refunds = $this->stripe->refunds->all($params);

        foreach ($refunds->data ?? [] as $refund) {
            $this->storeRefundPayload($this->toArray($refund));
        }
    }

    protected function syncSubscriptions(): void
    {
        if (! $this->stripe) {
            return;
        }

        $subscriptions = $this->stripe->subscriptions->all([
            'limit' => 100,
            'status' => 'all',
            'expand' => ['data.latest_invoice', 'data.customer', 'data.items.data.price'],
        ]);

        foreach ($subscriptions->data ?? [] as $subscription) {
            $this->storeSubscriptionPayload($this->toArray($subscription));
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    public function storeInvoicePayload(array $data): void
    {
        $invoiceId = $data['id'] ?? null;

        if (! $invoiceId) {
            return;
        }

        $metadata = $this->metadata($data);
        $line = $data['lines']['data'][0] ?? [];
        $orgId = $this->resolveOrgId($data['customer'] ?? null, $metadata);
        $planSlug = $this->resolvePlanSlug($metadata, $line['price']['id'] ?? null);

        $amount = (int) ($data['amount_paid'] ?? $data['amount_due'] ?? ($line['amount'] ?? 0));
        $currency = $data['currency'] ?? 'usd';
        $paidAt = $data['status_transitions']['paid_at'] ?? null;
        $periodStart = $line['period']['start'] ?? null;
        $periodEnd = $line['period']['end'] ?? null;

        BillingInvoice::updateOrCreate(
            ['stripe_invoice_id' => $invoiceId],
            [
                'org_id' => $orgId,
                'plan_slug' => $planSlug,
                'status' => $data['status'] ?? 'unknown',
                'amount' => $amount,
                'currency' => $currency,
                'period_start' => $this->toDate($periodStart),
                'period_end' => $this->toDate($periodEnd),
                'paid_at' => $this->toDate($paidAt ?? $data['created'] ?? null),
            ]
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public function storePaymentPayload(array $data): void
    {
        $paymentId = $data['id'] ?? null;

        if (! $paymentId) {
            return;
        }

        $metadata = $this->metadata($data);
        $amount = (int) ($data['amount_received'] ?? $data['amount'] ?? 0);
        $currency = $data['currency'] ?? 'usd';

        BillingPayment::updateOrCreate(
            ['stripe_payment_id' => $paymentId],
            [
                'org_id' => $this->resolveOrgId($data['customer'] ?? null, $metadata),
                'plan_slug' => $this->resolvePlanSlug($metadata),
                'stripe_charge_id' => $data['latest_charge'] ?? ($data['charges']['data'][0]['id'] ?? null),
                'status' => $data['status'] ?? 'unknown',
                'amount' => $amount,
                'currency' => $currency,
                'paid_at' => $this->toDate($data['created'] ?? null),
            ]
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public function storeRefundPayload(array $data): void
    {
        $refundId = $data['id'] ?? null;

        if (! $refundId) {
            return;
        }

        $metadata = $this->metadata($data);

        BillingRefund::updateOrCreate(
            ['stripe_refund_id' => $refundId],
            [
                'org_id' => $this->resolveOrgIdFromRefund($data, $metadata),
                'plan_slug' => $this->resolvePlanSlug($metadata),
                'stripe_charge_id' => $data['charge'] ?? null,
                'amount' => (int) ($data['amount'] ?? 0),
                'currency' => $data['currency'] ?? 'usd',
                'reason' => $data['reason'] ?? null,
                'refunded_at' => $this->toDate($data['created'] ?? null),
            ]
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public function storeSubscriptionPayload(array $data): void
    {
        $subId = $data['id'] ?? null;

        if (! $subId) {
            return;
        }

        $metadata = $this->metadata($data);
        $item = $data['items']['data'][0] ?? [];
        $price = $item['price'] ?? [];
        $planSlug = $this->resolvePlanSlug($metadata, $price['id'] ?? null);
        $quantity = (int) ($item['quantity'] ?? 1);
        $amount = (int) ($price['unit_amount'] ?? 0) * max(1, $quantity);
        $interval = $price['recurring']['interval'] ?? 'month';
        $intervalCount = (int) ($price['recurring']['interval_count'] ?? 1);

        BillingSubscription::updateOrCreate(
            ['stripe_subscription_id' => $subId],
            [
                'org_id' => $this->resolveOrgId($data['customer'] ?? null, $metadata),
                'plan_slug' => $planSlug,
                'status' => $data['status'] ?? 'unknown',
                'amount' => $amount,
                'currency' => $price['currency'] ?? 'usd',
                'interval' => $interval,
                'interval_count' => $intervalCount,
                'cancel_at_period_end' => (bool) ($data['cancel_at_period_end'] ?? false),
                'current_period_start' => $this->toDate($data['current_period_start'] ?? null),
                'current_period_end' => $this->toDate($data['current_period_end'] ?? null),
                'ended_at' => $this->toDate($data['ended_at'] ?? $data['canceled_at'] ?? null),
            ]
        );
    }

    /**
     * @param mixed $object
     * @return array<string, mixed>
     */
    protected function toArray($object): array
    {
        if (is_array($object)) {
            return $object;
        }

        if (is_object($object) && method_exists($object, 'toArray')) {
            /** @var array<string, mixed> $asArray */
            $asArray = $object->toArray();

            return $asArray;
        }

        return (array) $object;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function metadata(array $data): array
    {
        $metadata = $data['metadata'] ?? [];

        if (is_object($metadata) && method_exists($metadata, 'toArray')) {
            $metadata = $metadata->toArray();
        }

        return is_array($metadata) ? $metadata : [];
    }

    protected function resolvePlanSlug(array $metadata, ?string $priceId = null): ?string
    {
        $planSlug = $metadata['plan_slug'] ?? null;

        if ($planSlug) {
            return $planSlug;
        }

        if ($priceId && array_key_exists($priceId, $this->priceToPlan)) {
            return $this->priceToPlan[$priceId];
        }

        return null;
    }

    /**
     * @param array<string, mixed> $refund
     */
    protected function resolveOrgIdFromRefund(array $refund, array $metadata): ?int
    {
        if (isset($metadata['org_id'])) {
            return (int) $metadata['org_id'];
        }

        $paymentIntent = $refund['payment_intent'] ?? null;
        $chargeId = $refund['charge'] ?? null;

        if ($paymentIntent) {
            $orgId = BillingPayment::where('stripe_payment_id', $paymentIntent)->value('org_id');
            if ($orgId) {
                return (int) $orgId;
            }
        }

        if ($chargeId) {
            $orgId = BillingPayment::where('stripe_charge_id', $chargeId)->value('org_id');
            if ($orgId) {
                return (int) $orgId;
            }
        }

        return null;
    }

    protected function resolveOrgId($customerId, array $metadata): ?int
    {
        $orgId = $metadata['org_id'] ?? null;

        if ($orgId) {
            return (int) $orgId;
        }

        if ($customerId && is_array($customerId)) {
            $customerId = $customerId['id'] ?? null;
        } elseif (is_object($customerId)) {
            $customerId = $customerId->id ?? null;
        }

        if (! $customerId) {
            return null;
        }

        if (! array_key_exists($customerId, $this->customerToOrg)) {
            $this->customerToOrg[$customerId] = Org::where('stripe_customer_id', $customerId)->value('id');
        }

        return $this->customerToOrg[$customerId];
    }

    protected function toDate($timestamp): ?Carbon
    {
        if (empty($timestamp)) {
            return null;
        }

        try {
            return Carbon::createFromTimestamp((int) $timestamp);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function createdFilter(?Carbon $startDate, ?Carbon $endDate): array
    {
        $created = [];

        if ($startDate) {
            $created['gte'] = $startDate->startOfDay()->timestamp;
        }

        if ($endDate) {
            $created['lte'] = $endDate->endOfDay()->timestamp;
        }

        return $created;
    }
}
