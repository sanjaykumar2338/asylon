<x-admin-layout>
    <x-slot name="header">
        {{ __('Subscription Started') }}
    </x-slot>

    <div class="card card-outline card-success">
        <div class="card-body">
            <h3 class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i>{{ __('Checkout Complete') }}</h3>
            <p class="text-muted mb-3">
                {{ __('Thank you! Your subscription is being processed. We will finalize activation once Stripe confirms payment (via webhook).') }}
            </p>
            <a href="{{ route('dashboard') }}" class="btn btn-primary mr-2">
                {{ __('Return to Dashboard') }}
            </a>
            <a href="{{ route('billing.settings') }}" class="btn btn-outline-secondary">
                {{ __('View Billing Settings') }}
            </a>
        </div>
    </div>
</x-admin-layout>
