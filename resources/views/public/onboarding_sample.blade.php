<x-guest-layout container-class="w-full max-w-4xl mt-8 px-6 sm:px-10 py-10 bg-white shadow-md overflow-hidden sm:rounded-lg">
    <h1 class="text-3xl font-semibold text-gray-900 mb-4">Asylon HR Onboarding Consent Sample</h1>
    <p class="text-gray-700 mb-4">
        This static example shows the HR enrollment packet where staff provide their phone number and acknowledge they will receive internal safety/security alerts via SMS.
    </p>
    <div class="border rounded bg-gray-50 p-3">
        <img src="{{ asset('assets/images/onboarding-sample.png') }}" alt="Onboarding consent sample" class="w-full h-auto rounded">
    </div>
    <p class="text-sm text-gray-600 mt-3">
        Public link: <a href="{{ asset('assets/images/onboarding-sample.png') }}" class="text-indigo-600 underline">{{ asset('assets/images/onboarding-sample.png') }}</a>
    </p>
</x-guest-layout>
