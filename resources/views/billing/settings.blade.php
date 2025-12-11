<x-admin-layout>
    <x-slot name="header">
        {{ __('Billing Settings') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-credit-card mr-2"></i> {{ __('Subscription') }}
            </h3>
            @if ($org)
                <span class="badge badge-light border">{{ $org->billing_status ?? __('unknown') }}</span>
            @endif
        </div>
        <div class="card-body">
            <p class="mb-2">
                <strong>{{ __('Organization:') }}</strong> {{ $org?->name ?? __('N/A') }}
            </p>
            <p class="mb-3">
                <strong>{{ __('Current Plan:') }}</strong>
                {{ $plan?->name ?? __('No active subscription') }}
            </p>

            <div class="d-flex flex-column flex-sm-row align-items-start">
                @if ($org?->stripe_customer_id)
                    <form method="POST" action="{{ route('billing.portal') }}" class="mr-sm-3 mb-2 mb-sm-0">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-external-link-alt mr-1"></i> {{ __('Manage Subscription (Billing Portal)') }}
                        </button>
                    </form>
                @endif

                <a href="{{ route('billing.choose_plan') }}" class="btn btn-outline-secondary">
                    {{ __('Change Plan') }}
                </a>
            </div>
        </div>
    </div>
</x-admin-layout>
