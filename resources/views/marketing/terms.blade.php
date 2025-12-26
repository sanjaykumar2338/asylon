@extends('marketing.layout')

@section('title', 'Asylon | Terms of Use')

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>Terms Of Uses</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">Home </a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.terms') }}"> Terms Of Uses </a></span>
            </div>
        </div>
    </div>
</section>

<section class="policy-section">
    <div class="site-container">

        <div class="policy-content">
            <div class="inner-text-block">
                <h2>Acceptance of Terms</h2>
                <p>By accessing or using the Asylon platform ("Service"), you agree to be bound by these Terms of
                    Use. If you do not agree, you must discontinue use.</p>
            </div>
            <div class="inner-text-block">

                <h3>Purpose of the Platform</h3>
                <p>Asylon provides a secure reporting and threat-assessment system for schools, churches, and
                    organizations. Use of the platform must follow all applicable laws and your organization's
                    internal policies.</p>
            </div>

            <div class="inner-text-block">

                <h3>User Responsibilities</h3>
                <ul>
                    <li>Submit truthful and relevant information</li>
                    <li>Not misuse or disrupt the platform</li>
                    <li>Maintain the confidentiality of their login credentials</li>
                    <li>Use the Service only for authorized organizational purposes</li>
                </ul>
            </div>

            <div class="inner-text-block">


                <h3>Organization Responsibilities</h3>
                <ul>
                    <li>Manage user access based on role and need</li>
                    <li>Review and respond to reports in a timely manner</li>
                    <li>Maintain compliance with applicable laws and policies</li>
                    <li>Ensure correct handling of confidential information</li>
                </ul>
            </div>


            <div class="inner-text-block">


                <h3>Data Ownership</h3>
                <p>All report and case data belong to the organization that operates the account. Asylon acts as a
                    secure processor and custodian of that data.</p>
            </div>
        </div>

        <div class="policy-sidebar">
            <a href="{{ route('marketing.privacy') }}" class="policy-btn ">Privacy Policy</a>
            <a href="{{ route('marketing.terms') }}" class="policy-btn active">Terms Of Uses</a>
            <a href="{{ route('marketing.data_security') }}" class="policy-btn">Data Security & Hosting</a>
        </div>

    </div>
</section>
@endsection
