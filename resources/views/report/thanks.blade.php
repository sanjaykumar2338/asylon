<x-guest-layout>
    <div class="mx-auto max-w-lg text-center">
        <h1 class="text-2xl font-semibold text-gray-900">Thank you for your report</h1>
        <p class="mt-4 text-sm text-gray-600">
            Your report has been submitted successfully. If follow-up is required, keep this reference ID for future
            communication.
        </p>
        <p class="mt-3 text-sm font-medium text-indigo-700">
            {{ config('asylon.privacy.confirm') }}
        </p>

        <div class="mt-6 rounded-md border border-indigo-200 bg-indigo-50 p-4">
            <p class="text-sm font-medium text-indigo-900">Reference ID</p>
            <p class="mt-2 text-lg font-semibold text-indigo-700">{{ $id }}</p>
        </div>

        @if (!empty($followupUrl))
            <div class="mt-6 rounded-md border border-gray-200 bg-white p-4 text-left shadow-sm">
                <p class="text-sm font-medium text-gray-700">{{ __('Follow your case using this link') }}</p>
                <p class="mt-2 text-sm text-gray-600">
                    {{ __('Save this secure link to send updates or respond to reviewers anytime.') }}
                </p>
                <a href="{{ $followupUrl }}"
                    class="mt-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    {{ __('Open follow-up portal') }}
                </a>
            </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('report.create') }}" class="text-sm font-medium text-indigo-600 hover:underline">
                Submit another report
            </a>
            <span class="mx-2 text-gray-400">|</span>
            <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 hover:underline">
                Reviewer login
            </a>
        </div>
    </div>
</x-guest-layout>
