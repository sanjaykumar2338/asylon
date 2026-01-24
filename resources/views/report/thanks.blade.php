@extends('marketing.layout')

@section('title', 'Asylon | Report Submitted')

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
                <h2>Thank you</h2>
                <p>Your submission is secure, and we will begin reviewing it right away.</p>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">Home</a></span>
                <span>/</span>
                <span><a href="{{ route('report.create') }}">Submit a Report</a></span>
                <span>/</span>
                <span>Thank You</span>
            </div>
        </div>
    </div>
</section>

<section class="contact-asylon block-left thank-you-section" data-case-id="{{ $caseId }}">
    <div class="site-container">
        <div class="contact-grid">
            <div class="contact-bx">
                <div class="section-title">
                    <h2>Submission received</h2>
                    <p>We logged everything you shared and assigned it a Case ID for quick reference. Expect an acknowledgement within one business day.</p>
                </div>

                <div class="thank-you-card">
                    <h3>What happens next</h3>
                    <ul>
                        <li>We verify the details, triage severity, and loop in the right reviewers.</li>
                        <li>Keep this page open or record the Case ID so we can reconnect if we need clarification.</li>
                        @if ($followupUrl)
                            <li>Track progress via your personal follow-up link below.</li>
                        @endif
                    </ul>
                </div>

                <div class="action-buttons">
                    <button type="button" class="site-btn-dark" onclick="downloadScreenshot()">
                        Capture confirmation
                    </button>
                    <button type="button" class="site-btn-light" onclick="window.print()">
                        Print / save PDF
                    </button>
                </div>
            </div>

            <div class="contact-bx contact-right">
                <div class="case-id-card">
                    <div class="case-label">Case ID</div>
                    <div class="case-value">{{ $caseId ?: 'Pending' }}</div>
                </div>
                <div class="note">
                    <p>Need help right away?<br>
                        <a href="mailto:{{ $supportEmail }}" class="highlight-link">{{ $supportEmail }}</a>
                    </p>
                </div>

                @if ($followupUrl)
                    <div class="followup-link">
                        <p class="label">Follow-up link</p>
                        <a href="{{ $followupUrl }}" target="_blank" rel="noopener noreferrer" class="font-mono break-words">{{ $followupUrl }}</a>
                    </div>
                @endif

                <div class="quick-links">
                    <a href="{{ route('report.create') }}" class="font-semibold text-indigo-600 hover:underline">Submit another report</a>
                    <span class="separator">•</span>
                    <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:underline">Reviewer login</a>
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
