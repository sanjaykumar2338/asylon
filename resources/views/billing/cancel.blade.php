<x-admin-layout>
    <x-slot name="header">
        {{ __('Checkout Cancelled') }}
    </x-slot>

    <div class="card card-outline card-warning">
        <div class="card-body">
            <h3 class="mb-2"><i class="fas fa-info-circle text-warning mr-2"></i>{{ __('You cancelled the checkout') }}</h3>
            <p class="text-muted mb-3">
                {{ __('No charges were made. You can restart checkout at any time.') }}
            </p>
            <a href="{{ route('billing.choose_plan') }}" class="btn btn-primary mr-2">
                {{ __('Choose a Plan') }}
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                {{ __('Back to Dashboard') }}
            </a>
        </div>
    </div>
</x-admin-layout>
