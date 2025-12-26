@extends('marketing.layout')

@section('title', 'Asylon | Data Security & Hosting')

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>Data Security & Hosting</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">Home </a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.data_security') }}"> Data Security & Hosting </a></span>
            </div>
        </div>
    </div>
</section>

<section class="policy-section">
    <div class="site-container">

        <div class="policy-content">


            <div class="inner-text-block">
                <h2>Our Commitment to Security</h2>
                <p>Asylon is designed to protect sensitive information submitted by students, staff, members,
                    employees, and volunteers. The platform uses industry-standard security practices to safeguard
                    data throughout its lifecycle.</p>
            </div>
            <div class="inner-text-block">

                <h3>Hosting Environment</h3>
                <ul>
                    <li>Hosted on a secure cloud infrastructure (e.g., AWS, Azure, or equivalent)</li>
                    <li>Data centers meet modern security and compliance standards </li>
                    <li>Redundant systems ensure reliability and uptime</li>
                </ul>
            </div>

            <div class="inner-text-block">

                <h3>Organization Responsibilities</h3>
                <ul>
                    <li> Manage user access based on role and need</li>
                    <li> Review and respond to reports in a timely manner</li>
                    <li> Maintain compliance with applicable laws and policies</li>
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
            <a href="{{ route('marketing.terms') }}" class="policy-btn ">Terms Of Uses</a>
            <a href="{{ route('marketing.data_security') }}" class="policy-btn active">Data Security & Hosting</a>
        </div>

    </div>
</section>
@endsection
