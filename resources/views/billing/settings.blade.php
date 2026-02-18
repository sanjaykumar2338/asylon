<x-admin-layout>
    <x-slot name="header">
        {{ __('Billing Overview') }}
    </x-slot>

    @include('admin.partials.flash')

    @php
        $billingStatus = $org?->billing_status ?? 'pending';
        $statusLabel = match ($billingStatus) {
            'active' => __('Active'),
            'trialing' => __('Trial'),
            'canceling' => __('Canceling'),
            default => __('Pending / Trial'),
        };
        $statusStyle = match ($billingStatus) {
            'active' => 'success',
            'canceling' => 'warning',
            default => 'warning',
        };
        $hasActiveSubscription = $org?->hasActiveSubscription();
        $trialDays = $org?->trial_days_left;
        $cycleLabel = $billingCycle ?? __('Monthly');
        $nextBillingLabel = $nextBillingDate ?? __('Pending');
    @endphp

    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="card-title mb-0">
                    <i class="fas fa-credit-card mr-2"></i> {{ __('Subscription & Usage') }}
                </h3>
                <p class="small text-muted mb-0">
                    {{ __('Plan and billing information for your organization only.') }}
                </p>
            </div>
            <span class="badge badge-{{ $statusStyle }} text-uppercase">
                {{ $statusLabel }}
            </span>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="mb-1">
                        <strong>{{ __('Organization:') }}</strong> {{ $org?->name ?? __('N/A') }}
                    </p>
                    <p class="mb-0">
                        <strong>{{ __('Current Plan:') }}</strong>
                        {{ $plan?->name ?? __('Pending / Trial') }}
                    </p>
                    @if ($trialDays && $trialDays > 0)
                        <p class="text-muted small mb-0">
                            {{ __('Trial ends in :days days.', ['days' => $trialDays]) }}
                        </p>
                    @elseif (! $hasActiveSubscription)
                        <p class="text-muted small mb-0">
                            {{ __('This organization is not yet subscribed. Choose a plan to unlock features.') }}
                        </p>
                    @endif
                </div>
                <div class="col-md-6">
                    <p class="mb-1">
                        <strong>{{ __('Billing Cycle:') }}</strong> {{ $cycleLabel }}
                    </p>
                    <p class="mb-0">
                        <strong>{{ __('Next Billing Date:') }}</strong> {{ $nextBillingLabel }}
                    </p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted">{{ __('Reports this month') }}</small>
                        <div class="d-flex align-items-baseline">
                            <strong class="h4 mb-0">{{ $org?->reports_this_month_label ?? __('N/A') }}</strong>
                            <span class="ml-2 text-muted small">{{ __('Current / Limit') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted">{{ __('Seats used') }}</small>
                        <div class="d-flex align-items-baseline">
                            <strong class="h4 mb-0">{{ $org?->seats_used_label ?? __('N/A') }}</strong>
                            <span class="ml-2 text-muted small">{{ __('Including org admins & reviewers') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted">{{ __('Billing status') }}</small>
                        <p class="h4 mb-0 text-capitalize">{{ $billingStatus }}</p>
                        @if ($org?->stripe_customer_id)
                            <span class="text-muted small d-block">{{ __('Managed through Stripe portal') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-start gap-2">
                @if ($org?->stripe_customer_id && $hasActiveSubscription)
                    <form method="POST" action="{{ route('billing.portal') }}" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-external-link-alt mr-1"></i> {{ __('Manage Subscription (Billing Portal)') }}
                        </button>
                    </form>
                @endif

                <a href="{{ route('billing.choose_plan') }}"
                    class="btn {{ $hasActiveSubscription ? 'btn-outline-secondary' : 'btn-secondary' }}">
                    {{ $hasActiveSubscription ? __('Change Plan') : __('Pick a plan for this organization') }}
                </a>

                @if ($org?->stripe_subscription_id && $hasActiveSubscription)
                    <form method="POST" action="{{ route('billing.subscription.cancel') }}" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-times mr-1"></i> {{ __('Cancel Subscription') }}
                        </button>
                    </form>
                @endif
            </div>

            <p class="mt-3 text-muted small">
                {{ __('Upgrades take effect immediately. Downgrades and cancellations are honored at the end of the current billing period.') }}
            </p>
        </div>
    </div>
</x-admin-layout>
