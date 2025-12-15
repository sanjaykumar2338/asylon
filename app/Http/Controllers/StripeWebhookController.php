<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Models\Org;
use App\Models\Plan;
use App\Support\RevenueReporter;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    protected RevenueReporter $revenue;

    public function __construct(RevenueReporter $revenue)
    {
        $this->revenue = $revenue;
    }

    public function __invoke(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook');
        $event = null;

        if ($secret && class_exists(Webhook::class)) {
            try {
                $event = Webhook::constructEvent(
                    $payload,
                    $sigHeader,
                    $secret
                );
            } catch (SignatureVerificationException $e) {
                Log::warning('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
                return response()->noContent(Response::HTTP_BAD_REQUEST);
            } catch (\Throwable $e) {
                Log::warning('Stripe webhook parse failed', ['error' => $e->getMessage()]);
                return response()->noContent(Response::HTTP_BAD_REQUEST);
            }
        } else {
            $event = json_decode($payload, true);
        }

        $type = is_array($event) ? ($event['type'] ?? null) : ($event->type ?? null);
        $data = is_array($event) ? ($event['data']['object'] ?? []) : ($event->data->object ?? null);

        Log::info('Stripe webhook received', [
            'event_id' => is_array($event) ? ($event['id'] ?? null) : ($event->id ?? null),
            'type' => $type,
        ]);

        if (! $type || ! $data) {
            return response()->noContent();
        }

        switch ($type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSession($data);
                break;
            case 'customer.subscription.created':
            case 'customer.subscription.updated':
            case 'customer.subscription.deleted':
                $this->handleSubscriptionUpdate($data);
                $this->revenue->storeSubscriptionPayload($this->toArray($data));
                break;
            case 'payment_intent.succeeded':
                $this->revenue->storePaymentPayload($this->toArray($data));
                break;
            case 'invoice.payment_succeeded':
            case 'invoice.payment_failed':
                $this->handleInvoiceEvent($data);
                break;
            case 'charge.refunded':
            case 'refund.created':
                $this->handleRefundEvent($data);
                break;
        }

        return response()->noContent();
    }

    protected function handleCheckoutSession($session): void
    {
        $session = $this->toArray($session);
        $orgId = $session['metadata']['org_id'] ?? null;
        $planSlug = $session['metadata']['plan_slug'] ?? null;
        $interval = $session['metadata']['interval'] ?? null;

        if (! $orgId) {
            return;
        }

        $org = Org::find($orgId);

        if (! $org) {
            return;
        }

        $planId = $planSlug ? Plan::where('slug', $planSlug)->value('id') : null;

        $org->update([
            'plan_id' => $planId,
            'preferred_plan' => $planSlug ?: $org->preferred_plan,
            'billing_status' => 'active',
            'stripe_customer_id' => $session['customer'] ?? $org->stripe_customer_id,
            'stripe_subscription_id' => $session['subscription'] ?? $org->stripe_subscription_id,
        ]);
    }

    protected function handleSubscriptionUpdate($subscription): void
    {
        $subscription = $this->toArray($subscription);
        $metadata = $subscription['metadata'] ?? [];
        $orgId = $metadata['org_id'] ?? null;
        $planSlug = $metadata['plan_slug'] ?? null;

        if (! $orgId) {
            return;
        }

        $org = Org::find($orgId);

        if (! $org) {
            return;
        }

        $planId = $planSlug ? Plan::where('slug', $planSlug)->value('id') : null;
        $status = $subscription['status'] ?? null;
        $billingStatus = in_array($status, ['active', 'trialing'], true) ? 'active' : 'suspended';

        $org->update([
            'plan_id' => $planId,
            'preferred_plan' => $planSlug ?: $org->preferred_plan,
            'billing_status' => $billingStatus,
            'stripe_customer_id' => $subscription['customer'] ?? $org->stripe_customer_id,
            'stripe_subscription_id' => $subscription['id'] ?? $org->stripe_subscription_id,
        ]);
    }

    protected function handleInvoiceEvent($invoice): void
    {
        $invoice = $this->toArray($invoice);
        $this->revenue->storeInvoicePayload($invoice);

        $metadata = $invoice['metadata'] ?? [];
        $orgId = $metadata['org_id'] ?? null;

        if ($orgId && ($invoice['status'] ?? null) === 'paid') {
            Org::whereKey($orgId)->update([
                'billing_status' => 'active',
            ]);
        }
    }

    protected function handleRefundEvent($payload): void
    {
        $data = $this->toArray($payload);

        if (($data['object'] ?? null) === 'refund') {
            $this->revenue->storeRefundPayload($data);

            return;
        }

        if (($data['object'] ?? null) === 'charge' && ! empty($data['refunds']['data'])) {
            foreach ($data['refunds']['data'] as $refund) {
                $refundArray = $this->toArray($refund);

                if (! isset($refundArray['metadata']) && isset($data['metadata'])) {
                    $refundArray['metadata'] = $data['metadata'];
                }

                $this->revenue->storeRefundPayload($refundArray);
            }
        }
    }

    /**
     * @param mixed $payload
     * @return array<string, mixed>
     */
    protected function toArray($payload): array
    {
        if (is_array($payload)) {
            return $payload;
        }

        if (is_object($payload) && method_exists($payload, 'toArray')) {
            /** @var array<string, mixed> $payloadArray */
            $payloadArray = $payload->toArray();

            return $payloadArray;
        }

        return (array) $payload;
    }
}
