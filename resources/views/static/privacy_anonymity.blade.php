@extends('marketing.layout')

@section('title', 'Asylon | Privacy & Anonymity')

@section('content')
@php
    $supportEmail = config('asylon.support_email', 'support@asylon.cc');
    $infoEmail = config('asylon.info_email', 'info@asylon.cc');
@endphp

<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>Privacy &amp; Anonymity</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">Home</a></span>
                <span>/</span>
                <span><a href="{{ route('report.create') }}">Submit a Report</a></span>
                <span>/</span>
                <span><a href="{{ route('privacy.anonymity') }}">Privacy &amp; Anonymity</a></span>
            </div>
        </div>
    </div>
</section>

<section class="policy-section">
    <div class="site-container">
        <div class="policy-content">
            <div class="inner-text-block">
                <h2>How we protect reporters</h2>
                <p>
                    We keep your identity private unless you choose to share it. This page summarizes how privacy,
                    anonymity, and retention work on the reporting portal.
                </p>
            </div>

            <div class="inner-text-block">
                <h3>What we collect and what we do not</h3>
                <ul>
                    <li><strong>Report details you provide</strong> such as descriptions, attachments, and optional contact info.</li>
                    <li><strong>Optional follow-up contact</strong> if you choose to share your email or phone.</li>
                    <li><strong>We do not require names</strong> or personal identifiers to submit a report.</li>
                    <li><strong>No tracking cookies or analytics</strong> on reporting and follow-up pages.</li>
                </ul>
            </div>

            <div class="inner-text-block">
                <h3>How anonymity works</h3>
                <ul>
                    <li>You can submit anonymously; leave contact fields blank to stay anonymous.</li>
                    <li>If you share contact details, they are only used for follow-up about your report.</li>
                    <li>Voice recordings can be anonymized to disguise your voice.</li>
                    <li>Internal audit logs are limited to authorized reviewers and administrators.</li>
                </ul>
            </div>

            <div class="inner-text-block">
                <h3>How voice and attachments are stored</h3>
                <p>
                    Files are stored securely with access limited to authorized reviewers. Sensitive audio is queued for
                    anonymization, and all downloads are signed to prevent public access.
                </p>
            </div>

            <div class="inner-text-block">
                <h3>Data retention and deletion</h3>
                <p>
                    Reports and files are retained only as long as required by your organization policies. Deletion
                    requests can be made through your administrator. When the ultra-private mode is enabled, IP and
                    device details are suppressed on intake.
                </p>
            </div>

            <div class="inner-text-block">
                <div class="alert alert-success mb-0">
                    <p class="mb-1 fw-semibold">Questions?</p>
                    <p class="mb-0">
                        Contact <a href="mailto:{{ $infoEmail }}">{{ $infoEmail }}</a>
                        or <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>.
                    </p>
                </div>
            </div>
        </div>

        <div class="policy-sidebar">
            <a href="{{ route('privacy.anonymity') }}" class="policy-btn active">Privacy &amp; Anonymity</a>
            <a href="{{ route('security.overview') }}" class="policy-btn">Security Overview</a>
            <a href="{{ route('report.create') }}" class="policy-btn">Submit a Report</a>
        </div>
    </div>
</section>
@endsection
