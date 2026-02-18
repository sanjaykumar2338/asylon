<?php

namespace App\Services;

use App\Models\BillingEvent;
use App\Models\Org;
use App\Models\Plan;
use App\Support\RevenueReporter;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class StripeSubscriptionManager
{
    protected ?StripeClient $stripe = null;

    public function __construct(protected RevenueReporter $reporter)
    {
        if (class_exists(StripeClient::class) && config('services.stripe.secret')) {
            $this->stripe = new StripeClient(config('services.stripe.secret'));
        }
    }

    public function stripeEnabled(): bool
    {
        return $this->stripe !== null;
    }

    public function syncOrg(Org $org): array
    {
        if (! $this->stripeEnabled()) {
            return $this->error('Stripe is not configured.');
        }

        $subId = $org->stripe_subscription_id;

        if (! $subId) {
            return $this->error('No Stripe subscription id on org.');
        }

        try {
            $subscription = $this->stripe->subscriptions->retrieve($subId, [
                'expand' => ['items.data.price', 'latest_invoice'],
            ]);

            $oldStatus = $org->billing_status;
            $this->reporter->storeSubscriptionPayload($subscription->toArray());

            $status = $subscription->status ?? null;
            $billingStatus = in_array($status, ['active', 'trialing'], true) ? 'active' : ($status ?: 'pending');
            $org->update([
                'billing_status' => $billingStatus,
                'stripe_customer_id' => $subscription->customer ?? $org->stripe_customer_id,
            ]);

            $this->logEvent($org, 'status_changed', $oldStatus, $billingStatus, [
                'source' => 'sync',
                'stripe_subscription_id' => $subId,
            ]);

            return $this->ok('Subscription synced from Stripe.');
        } catch (\Throwable $e) {
            Log::warning('Stripe subscription sync failed', ['org_id' => $org->id, 'error' => $e->getMessage()]);

            return $this->error($e->getMessage());
        }
    }

    public function changePlan(Org $org, string $priceId, ?string $planSlug = null, ?int $actorId = null): array
    {
        if (! $this->stripeEnabled()) {
            return $this->error('Stripe is not configured.');
        }

        if (! $org->stripe_subscription_id) {
            return $this->error('No Stripe subscription is attached to this organization.');
        }

        try {
            $subscription = $this->stripe->subscriptions->retrieve($org->stripe_subscription_id, ['expand' => ['items']]);
            $item = $subscription->items->data[0] ?? null;

            if (! $item) {
                return $this->error('Subscription item not found.');
            }

            $updated = $this->stripe->subscriptions->update($subscription->id, [
                'items' => [
                    [
                        'id' => $item->id,
                        'price' => $priceId,
                    ],
                ],
                'metadata' => array_merge($subscription->metadata ?? [], [
                    'org_id' => $org->id,
                ]),
            ]);

            $this->reporter->storeSubscriptionPayload($updated->toArray());

            if ($planSlug) {
                $newPlanId = Plan::where('slug', $planSlug)->value('id');
                if ($newPlanId) {
                    $org->update([
                        'plan_id' => $newPlanId,
                        'billing_status' => 'active',
                    ]);
                }
            }

            $this->logEvent($org, 'plan_changed', $org->plan?->slug, $planSlug ?? $priceId, [
                'stripe_subscription_id' => $subscription->id,
                'stripe_price_id' => $priceId,
                'plan_slug' => $planSlug,
                'actor_id' => $actorId,
            ]);

            return $this->ok('Plan updated.');
        } catch (\Throwable $e) {
            Log::warning('Stripe change plan failed', ['org_id' => $org->id, 'error' => $e->getMessage()]);

            return $this->error($e->getMessage());
        }
    }

    public function cancelAtPeriodEnd(Org $org, ?int $actorId = null): array
    {
        return $this->updateCancelFlag($org, true, $actorId);
    }

    public function resume(Org $org, ?int $actorId = null): array
    {
        return $this->updateCancelFlag($org, false, $actorId);
    }

    public function setStatusOverride(Org $org, string $status, ?int $actorId = null): array
    {
        $old = $org->billing_status;
        $org->update(['billing_status' => $status]);

        $this->logEvent($org, 'status_changed', $old, $status, [
            'source' => 'manual_override',
            'actor_id' => $actorId,
        ]);

        return $this->ok('Status updated.');
    }

    protected function updateCancelFlag(Org $org, bool $cancel, ?int $actorId = null): array
    {
        if (! $this->stripeEnabled()) {
            return $this->error('Stripe is not configured.');
        }

        if (! $org->stripe_subscription_id) {
            return $this->error('No Stripe subscription is attached to this organization.');
        }

        try {
            $subscription = $this->stripe->subscriptions->update($org->stripe_subscription_id, [
                'cancel_at_period_end' => $cancel,
            ]);

            $this->reporter->storeSubscriptionPayload($subscription->toArray());

            $this->logEvent($org, $cancel ? 'cancel_requested' : 'reactivated', null, null, [
                'cancel_at_period_end' => $cancel,
                'actor_id' => $actorId,
                'stripe_subscription_id' => $subscription->id,
            ]);

            return $this->ok($cancel ? 'Cancellation scheduled.' : 'Subscription resumed.');
        } catch (\Throwable $e) {
            Log::warning('Stripe cancel/resume failed', ['org_id' => $org->id, 'error' => $e->getMessage()]);

            return $this->error($e->getMessage());
        }
    }

    protected function logEvent(Org $org, string $type, ?string $old, ?string $new, array $meta = []): void
    {
        BillingEvent::create([
            'org_id' => $org->id,
            'type' => $type,
            'old_value' => $old,
            'new_value' => $new,
            'meta' => $meta ?: null,
            'created_by' => $meta['actor_id'] ?? null,
        ]);
    }

    protected function ok(string $message): array
    {
        return ['ok' => true, 'message' => $message];
    }

    protected function error(string $message): array
    {
        return ['ok' => false, 'message' => $message];
    }
}
