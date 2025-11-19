<x-guest-layout>
    @php
        $caseId = $report->public_reference ?? $report->id ?? $id ?? '';
    @endphp
    <div class="mx-auto max-w-lg text-center" id="thankyou-card" data-case-id="{{ $caseId }}">
        <h1 class="text-2xl font-semibold text-gray-900">{{ __('Thank you for your report') }}</h1>
        <p class="mt-4 text-sm text-gray-600">
            {{ __('Your report has been submitted successfully. If follow-up is required, keep this Case ID for future communication.') }}
        </p>
        <p class="mt-3 text-sm font-medium text-indigo-700">
            {{ config('asylon.privacy.confirm') }}
        </p>

        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded text-left">
            <p class="font-semibold text-yellow-800">
                {{ __('Important: Save your Case ID') }}
            </p>
            <p class="text-sm text-yellow-800 mt-1">
                {{ __('Your Case ID') }}:
                <strong class="font-mono text-base">{{ $caseId }}</strong><br>
                {{ __('Please write this down or take a screenshot. You\'ll need it if you want to follow up on this case later.') }}
            </p>
            <div class="mt-3 grid gap-2 sm:grid-cols-2 sm:max-w-md">
                <button type="button"
                    class="w-full inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-black shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    onclick="downloadScreenshot()">
                    {{ __('Take screenshot') }}
                </button>
                <button type="button"
                    class="w-full inline-flex justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    onclick="window.print()">
                    {{ __('Download PDF') }}
                </button>
            </div>
        </div>

        @if (!empty($followupUrl))
            <p class="mt-4 text-sm">
                {{ __('To check updates later, use this link:') }}
                <a href="{{ $followupUrl }}" class="text-indigo-600 underline">{{ $followupUrl }}</a>
            </p>
            <div class="mt-4">
                <a href="{{ $followupUrl }}"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    {{ __('Open follow-up portal') }}
                </a>
            </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('report.create') }}" class="text-sm font-medium text-indigo-600 hover:underline">
                {{ __('Submit another report') }}
            </a>
            <span class="mx-2 text-gray-400">|</span>
            <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 hover:underline">
                {{ __('Reviewer login') }}
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.downloadScreenshot = function () {
                const target = document.getElementById('thankyou-card');
                if (!target || typeof html2canvas !== 'function') {
                    return;
                }

                html2canvas(target, { scale: 2 }).then(function (canvas) {
                    canvas.toBlob(function (blob) {
                        if (!blob) {
                            return;
                        }
                        const link = document.createElement('a');
                        const caseId = target.dataset.caseId || 'case';
                        link.download = caseId + '-thank-you.png';
                        link.href = URL.createObjectURL(blob);
                        link.click();
                        URL.revokeObjectURL(link.href);
                    });
                }).catch(function (error) {
                    console.error('Screenshot failed', error);
                });
            };
        });
    </script>
</x-guest-layout>
