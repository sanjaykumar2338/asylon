<x-guest-layout>
    @php
        $caseId = $report->public_reference ?? $report->id ?? $id ?? '';
    @endphp
    <section class="mx-auto max-w-4xl px-4 py-10">
        <div class="bg-[#111f3c] text-white rounded-3xl px-6 py-10 shadow-lg">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-semibold">{{ __('Thank you for your report') }}</h1>
                    <p class="mt-2 text-white/70 max-w-2xl">
                        {{ __('We received your submission and will follow our security process. Use the case number below if you need to follow up later.') }}
                    </p>
                </div>
                <span class="inline-flex items-center rounded-full bg-white/10 px-4 py-1 text-sm font-semibold tracking-wide">
                    {{ __('Case ID') }}:
                    <span class="ml-2 font-mono">{{ $caseId }}</span>
                </span>
            </div>
        </div>

        <div class="mt-8 grid gap-6 md:grid-cols-2">
            <div class="rounded-2xl border border-gray-200 bg-white px-6 py-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">{{ __('Next steps') }}</h2>
                        <p class="text-sm text-gray-600">
                            {{ __('Take a screenshot or copy the case link so you can reference it later.') }}
                        </p>
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-3">
                    <button type="button"
                        class="flex items-center justify-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-900 transition hover:border-gray-400"
                        onclick="downloadScreenshot()">
                        <i class="fa-solid fa-camera"></i>
                        {{ __('Capture confirmation card') }}
                    </button>
                    <button type="button"
                        class="flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                        onclick="window.print()">
                        <i class="fa-solid fa-print"></i>
                        {{ __('Print or save as PDF') }}
                    </button>
                </div>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-6 py-5 shadow-sm">
                <h2 class="text-base font-semibold text-gray-900">{{ __('Case details') }}</h2>
                <dl class="mt-4 space-y-3 text-sm text-gray-600">
                    <div>
                        <dt class="font-medium text-gray-800">{{ __('Submitted at') }}</dt>
                        <dd>{{ now()->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-800">{{ __('Status') }}</dt>
                        <dd>{{ __('Under review') }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-800">{{ __('Follow-up link') }}</dt>
                        @if ($followupUrl)
                            <dd>
                                <a href="{{ $followupUrl }}" class="text-indigo-600 underline">
                                    {{ __('Open follow-up portal') }}
                                </a>
                            </dd>
                        @else
                            <dd>{{ __('Link will be available shortly.') }}</dd>
                        @endif
                    </div>
                </dl>
            </div>
        </div>

        <div class="mt-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Need help?') }}</h3>
                    <p class="text-sm text-gray-600">
                        {{ __('Our team is here 24/7. Email us with your case ID for faster support.') }}
                    </p>
                </div>
                <a href="mailto:{{ config('asylon.support_email', 'support@asylon.app') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-800">
                    <i class="fa-solid fa-envelope"></i>
                    {{ config('asylon.support_email', 'support@asylon.app') }}
                </a>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.downloadScreenshot = function () {
                const target = document.querySelector('section');
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
                        link.download = caseId + '-confirmation.png';
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
