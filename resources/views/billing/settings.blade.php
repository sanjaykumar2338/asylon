<x-admin-layout>
    <x-slot name="header">
        {{ __('Billing Settings') }}
    </x-slot>

    @include('admin.partials.flash')

    @php
        $billingStatus = $org?->billing_status ?? 'pending';
        $statusLabel = $billingStatus === 'active'
            ? __('Active')
            : ($billingStatus === 'trialing' ? __('Trial') : __('Pending / Trial'));
        $statusStyle = $billingStatus === 'active' ? 'success' : 'warning';
        $hasSubscription = $org && $billingStatus === 'active' && $plan;
        $trialDays = $org?->trial_days_left;
    @endphp

    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-credit-card mr-2"></i> {{ __('Subscription & Usage') }}
            </h3>
            <span class="badge badge-{{ $statusStyle }} text-uppercase">
                {{ $statusLabel }}
            </span>
        </div>
        <div class="card-body">
            <p class="mb-1">
                <strong>{{ __('Organization:') }}</strong> {{ $org?->name ?? __('N/A') }}
            </p>
            <p class="mb-2">
                <strong>{{ __('Current Plan:') }}</strong>
                {{ $plan?->name ?? __('Pending / Trial') }}
            </p>
            @if ($trialDays && $trialDays > 0)
                <p class="mb-3 text-muted small">
                    {{ __('Trial ends in :days days.', ['days' => $trialDays]) }}
                </p>
            @elseif (! $hasSubscription)
                <p class="mb-3 text-muted small">
                    {{ __('This organization currently does not have an active subscription. Pending or trialing teams can start or upgrade a plan below.') }}
                </p>
            @endif

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
                @if ($org?->stripe_customer_id && $org?->billing_status === 'active')
                    <form method="POST" action="{{ route('billing.portal') }}" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-external-link-alt mr-1"></i> {{ __('Manage Subscription (Billing Portal)') }}
                        </button>
                    </form>
                @endif

                @if (auth()->user()?->isOrgAdmin() || auth()->user()?->hasRole(['platform_admin', 'super_admin']))
                    <a href="{{ route('billing.choose_plan') }}"
                        class="btn {{ $hasSubscription ? 'btn-outline-secondary' : 'btn-secondary' }}">
                        {{ $hasSubscription ? __('Change Plan') : __('Pick a plan for this organization') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
