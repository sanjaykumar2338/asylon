@extends('marketing.layout')

@section('title', 'Asylon | Security Overview')

@section('content')
@php
    $supportEmail = config('asylon.support_email', 'support@asylon.cc');
    $infoEmail = config('asylon.info_email', 'info@asylon.cc');
@endphp

<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>Security Overview</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">Home</a></span>
                <span>/</span>
                <span><a href="{{ route('report.create') }}">Submit a Report</a></span>
                <span>/</span>
                <span><a href="{{ route('security.overview') }}">Security Overview</a></span>
            </div>
        </div>
    </div>
</section>

<section class="policy-section">
    <div class="site-container">
        <div class="policy-content">
            <div class="inner-text-block">
                <h2>How we secure reports</h2>
                <p>
                    A quick summary of how we protect reports, attachments, and follow-up conversations on the Asylon
                    reporting portal.
                </p>
            </div>

            <div class="inner-text-block">
                <h3>Data in transit and at rest</h3>
                <ul>
                    <li>HTTPS everywhere with signed links for attachment previews and downloads.</li>
                    <li>Stored files are protected in restricted storage with access limited to authorized reviewers.</li>
                    <li>Audit logs track administrative actions for accountability.</li>
                </ul>
            </div>

            <div class="inner-text-block">
                <h3>Anonymity safeguards</h3>
                <ul>
                    <li>Anonymous submissions by default; contact info is optional.</li>
                    <li>Voice anonymization is available for uploaded or recorded audio.</li>
                    <li>IP and device details are suppressed when ultra-private mode is enabled.</li>
                </ul>
            </div>

            <div class="inner-text-block">
                <h3>Data retention and access</h3>
                <ul>
                    <li>Retention follows your organization policies; data can be removed on authorized requests.</li>
                    <li>Role-based access limits who can view or act on reports.</li>
                    <li>No third-party analytics or tracking on reporting portals.</li>
                </ul>
            </div>

            <div class="inner-text-block">
                <h3>Operational practices</h3>
                <ul>
                    <li>Signed URLs for attachments expire and cannot be guessed.</li>
                    <li>Alerts are routed to authorized administrators and reviewers only.</li>
                    <li>Incident response playbooks ensure timely review of urgent cases.</li>
                </ul>
            </div>

            <div class="inner-text-block">
                <div class="alert alert-success mb-0">
                    <p class="mb-1 fw-semibold">Questions or requests?</p>
                    <p class="mb-0">
                        Reach us at <a href="mailto:{{ $infoEmail }}">{{ $infoEmail }}</a>
                        or <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>.
                    </p>
                </div>
            </div>
        </div>

        <div class="policy-sidebar">
            <a href="{{ route('privacy.anonymity') }}" class="policy-btn">Privacy &amp; Anonymity</a>
            <a href="{{ route('security.overview') }}" class="policy-btn active">Security Overview</a>
            <a href="{{ route('report.create') }}" class="policy-btn">Submit a Report</a>
        </div>
    </div>
</section>
@endsection
