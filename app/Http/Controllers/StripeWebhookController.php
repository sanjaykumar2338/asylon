<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Models\Org;
use App\Models\Plan;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
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
            case 'invoice.payment_succeeded':
                $this->handleSubscriptionUpdate($data);
                break;
        }

        return response()->noContent();
    }

    protected function handleCheckoutSession($session): void
    {
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

        $org->update([
            'plan_id' => $planId,
            'preferred_plan' => $planSlug ?: $org->preferred_plan,
            'billing_status' => 'active',
            'stripe_customer_id' => $subscription['customer'] ?? $org->stripe_customer_id,
            'stripe_subscription_id' => $subscription['id'] ?? $org->stripe_subscription_id,
        ]);
    }
}
