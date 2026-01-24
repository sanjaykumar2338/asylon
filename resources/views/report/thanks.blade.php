<x-guest-layout>
    @php
        $caseId = $report->public_reference ?? $report->id ?? $id ?? '';
    @endphp
    <section class="min-h-screen bg-[#eef2ff] py-12">
        <div class="mx-auto max-w-5xl px-4">
            <div class="bg-white shadow-[0_40px_80px_rgba(15,23,42,.08)] rounded-3xl overflow-hidden border border-gray-100">
                <div class="grid md:grid-cols-[1.05fr,1fr]">
                    <div class="bg-gradient-to-br from-[#0b1f3b] to-[#173f74] text-white p-10 flex flex-col justify-between gap-8">
                        <div>
                            <p class="text-xs uppercase tracking-[0.5em] text-white/60 mb-3">{{ __('Thank you') }}</p>
                            <h1 class="text-4xl font-semibold leading-tight">{{ __('Your report is in good hands') }}</h1>
                            <p class="mt-4 text-white/70 leading-relaxed text-sm md:text-base">
                                {{ __('We logged your submission and will activate our response process. Hold onto your Case ID so we can reconnect if follow-up is needed.') }}
                            </p>
                        </div>
                        <div class="rounded-3xl bg-white/10 p-4 flex flex-col gap-1">
                            <span class="text-xs uppercase tracking-[0.5em] text-white/60">{{ __('Case ID') }}</span>
                            <span class="font-mono text-2xl md:text-3xl">{{ $caseId }}</span>
                        </div>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-[#eaecf8] text-[#0b1f3b]">
                                <i class="fa-solid fa-shield-check"></i>
                            </span>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">{{ __('Submission complete') }}</h2>
                                <p class="text-sm text-gray-600">{{ __('You may leave this page or submit again anytime.') }}</p>
                            </div>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <button type="button"
                                class="flex items-center justify-center gap-2 rounded-2xl border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-900 transition hover:border-gray-400"
                                onclick="downloadScreenshot()">
                                <i class="fa-solid fa-camera"></i>
                                {{ __('Capture confirmation card') }}
                            </button>
                            <button type="button"
                                class="flex items-center justify-center gap-2 rounded-2xl bg-[#0b1f3b] px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#09172d]"
                                onclick="window.print()">
                                <i class="fa-solid fa-print"></i>
                                {{ __('Print or save PDF') }}
                            </button>
                        </div>
                        @if ($followupUrl)
                            <div class="rounded-2xl border border-dashed border-indigo-200 bg-indigo-50/70 p-4 text-sm text-indigo-700">
                                {{ __('Check updates anytime with this follow-up link:') }}
                                <div class="break-all font-mono text-xs text-indigo-900 mt-1">
                                    <a href="{{ $followupUrl }}" target="_blank" rel="noopener">{{ $followupUrl }}</a>
                                </div>
                            </div>
                        @endif
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5 text-sm text-gray-600">
                            <p>{{ __('Need help immediately?') }}</p>
                            <a href="mailto:{{ config('asylon.support_email', 'support@asylon.app') }}"
                               class="font-semibold text-indigo-600 hover:underline">
                                {{ config('asylon.support_email', 'support@asylon.app') }}
                            </a>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                            <a href="{{ route('report.create') }}" class="font-semibold text-indigo-600 hover:underline">
                                {{ __('Submit another report') }}
                            </a>
                            <span class="text-gray-300">•</span>
                            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:underline">
                                {{ __('Reviewer login') }}
                            </a>
                        </div>
                    </div>
                </div>
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
