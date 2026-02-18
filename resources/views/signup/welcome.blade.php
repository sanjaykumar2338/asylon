<x-guest-layout>
    <div class="text-center space-y-3">
        <h1 class="text-2xl font-bold">{{ __('Welcome to Asylon!') }}</h1>
        <p class="text-gray-600">{{ __('Your organization has been created. You are logged in as the executive admin.') }}</p>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            {{ __('Go to Dashboard') }}
        </a>
    </div>
</x-guest-layout>
