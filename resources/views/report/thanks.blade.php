@extends('marketing.layout')

@section('title', __('report.thanks_page.page_title'))

@section('content')
@php
    $caseId = $report->public_reference ?? $report->id ?? $id ?? '';
    $supportEmail = config('asylon.support_email', 'support@asylon.app');
    $followupUrl = $followupUrl ?? null;
@endphp

<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>{{ __('report.thanks_page.header_title') }}</h2>
                <p>{{ __('report.thanks_page.header_subtitle') }}</p>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">{{ __('frontend.nav.home') }}</a></span>
                <span>/</span>
                <span><a href="{{ route('report.create') }}">{{ __('report.breadcrumb.submit') }}</a></span>
                <span>/</span>
                <span>{{ __('report.thanks_page.breadcrumb') }}</span>
            </div>
        </div>
    </div>
</section>

<section class="contact-asylon block-left thank-you-section" data-case-id="{{ $caseId }}">
    <div class="site-container">
        <div class="contact-grid">
            <div class="contact-bx">
                <div class="section-title">
                    <h2>{{ __('report.thanks_page.section_title') }}</h2>
                    <p>{{ __('report.thanks_page.section_body') }}</p>
                </div>

                <div class="thank-you-card">
                    <h3>{{ __('report.thanks_page.next_title') }}</h3>
                    <ul>
                        <li>{{ __('report.thanks_page.next_item_one') }}</li>
                        <li>{{ __('report.thanks_page.next_item_two') }}</li>
                        @if ($followupUrl)
                            <li>{{ __('report.thanks_page.next_item_three') }}</li>
                        @endif
                    </ul>
                </div>

                <div class="action-buttons">
                    <button type="button" class="site-btn-dark" onclick="downloadScreenshot()">
                        {{ __('report.thanks_page.cta_capture') }}
                    </button>
                    <button type="button" class="site-btn-light" onclick="window.print()">
                        {{ __('report.thanks_page.cta_print') }}
                    </button>
                </div>
            </div>

            <div class="contact-bx contact-right">
                <div class="case-id-card">
                    <div class="case-label">{{ __('report.thanks_page.case_id_label') }}</div>
                    <div class="case-value">{{ $caseId ?: __('report.thanks_page.case_id_pending') }}</div>
                </div>
                <div class="note">
                    <p>{{ __('report.thanks_page.need_help') }}<br>
                        <a href="mailto:{{ $supportEmail }}" class="highlight-link">{{ $supportEmail }}</a>
                    </p>
                </div>

                @if ($followupUrl)
                    <div class="followup-link">
                        <p class="label">{{ __('report.thanks_page.followup_label') }}</p>
                        <a href="{{ $followupUrl }}" target="_blank" rel="noopener noreferrer" class="font-mono break-words">{{ $followupUrl }}</a>
                    </div>
                @endif

                <div class="quick-links">
                    <a href="{{ route('report.create') }}" class="font-semibold text-indigo-600 hover:underline">{{ __('report.thanks_page.submit_another') }}</a>
                    <span class="separator">•</span>
                    <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:underline">{{ __('report.thanks_page.reviewer_login') }}</a>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.downloadScreenshot = function () {
                const target = document.querySelector('.thank-you-section');
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
@endpush
@endsection
