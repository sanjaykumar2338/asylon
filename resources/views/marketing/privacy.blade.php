@extends('marketing.layout')

@section('title', 'Asylon | Privacy Policy')

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>Privacy Policy</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">Home </a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.privacy') }}"> Privacy Policy </a></span>
            </div>
        </div>
    </div>
</section>

<section class="policy-section">
    <div class="site-container">

        <div class="policy-content">
            <div class="inner-text-block">
                <h2>Introduction</h2>
                <p>
                    Asylon ("we," "our," "us") is committed to protecting the privacy of the
                    students, staff, members, volunteers, and employees who use our platform.
                    This Privacy Policy explains how we collect, use, store, and protect
                    information submitted through Asylon.
                </p>
            </div>
            <div class="inner-text-block">

                <h3>1. Information Submitted Through Reports</h3>
                <ul>
                    <li>Text descriptions</li>
                    <li>Names and contact information (optional)</li>
                    <li>Uploaded files (images, documents, screenshots)</li>
                    <li>Location or campus information</li>
                    <li>Anonymous submissions (if enabled by the organization)</li>
                </ul>
            </div>

            <div class="inner-text-block">

                <h3>2. Account & User Information</h3>
                <ul>
                    <li>Names and roles of administrators and reviewers</li>
                    <li>Work email addresses</li>
                    <li>Organization details</li>
                </ul>
            </div>

            <div class="inner-text-block">


                <h3>3. Platform Usage Information</h3>
                <ul>
                    <li>IP address</li>
                    <li>Browser and device information</li>
                    <li>Timestamps</li>
                    <li>Audit logs of account activity</li>
                </ul>
            </div>


            <div class="inner-text-block">


                <h3>4. How We Use Information</h3>
                <ul>
                    <li>Delivering reporting, review, and case-management services</li>
                    <li>Notifying authorized team members about new reports</li>
                    <li>Improving platform features and reliability</li>
                    <li>Providing customer support and troubleshooting</li>
                    <li>Anonymized analytics and trend reporting</li>
                </ul>
            </div>
        </div>

        <div class="policy-sidebar">
            <a href="{{ route('marketing.privacy') }}" class="policy-btn active">Privacy Policy</a>
            <a href="{{ route('marketing.terms') }}" class="policy-btn">Terms Of Uses</a>
            <a href="{{ route('marketing.data_security') }}" class="policy-btn">Data Security & Hosting</a>
        </div>

    </div>
</section>
@endsection
