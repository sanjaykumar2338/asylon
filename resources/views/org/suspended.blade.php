<x-guest-layout>
    <div class="text-center space-y-3">
        <h1 class="text-2xl font-bold">{{ __('Organization Suspended') }}</h1>
        <p class="text-gray-600">{{ __('Access is temporarily suspended. Please contact support to restore access.') }}</p>
        <div class="text-gray-600">{{ __('Support: ') }}{{ config('asylon.support_email', 'support@example.com') }}</div>
    </div>
</x-guest-layout>
