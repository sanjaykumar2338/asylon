@extends('marketing.layout')

@section('title', 'Asylon | Solutions - Organization')

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>Solutions</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">Home </a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.solutions.organizations') }}">Solutions</a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.solutions.organizations') }}">Organizations</a></span>

            </div>

        </div>
    </div>
</section>

<section class="report-section block-p cta-w1">
    <div class="site-container">
        <div class="report-grid align-items-center">


            <div class="report-bx">
                <div class="section-header">

                    <div class="section-subtitle">
                        <span>Organization</span>
                    </div>

                    <div class="section-title">
                        <h2>A Trusted Channel for <br> Workplace Safety and <br> Conduct</h2>
                    </div>
                    <p>Enable employees to report threats, harassment, and policy violations while HR, legal, and
                        security teams see every case in one secure system.</p>

                    <div class="action-btn">
                        <a href="{{ route('marketing.demo') }}" class="site-btn-dark">Schedule an Organization Safety Consultation</a>
                    </div>
                </div>
            </div>


            <div class="report-bx">
                <div class="right-image">
                    <img src="{{ $assetBase }}/images/34Group.png" alt="">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="report-section block-p2 bg2 block-left">
    <div class="site-container">
        <div class="report-grid align-items-center">

            <div class="report-bx">
                <div class="right-image">
                    <img src="{{ $assetBase }}/images/22Group.png" alt="">
                </div>
            </div>


            <div class="report-bx">
                <div class="section-header">

                    <div class="section-subtitle">
                        <span>THE PROBLEM</span>
                    </div>

                    <div class="section-title">
                        <h2>Real Challenges <br> Organizations Faces</h2>
                    </div>

                    <ul class="site-list">
                        <li>Workplace violence and threats that go unreported</li>
                        <li>Harassment and discrimination concerns</li>
                        <li>Stalking or domestic issues that follow employees to work</li>
                        <li>Policy violations that are handled through scattered emails</li>
                    </ul>

                </div>
            </div>


        </div>
    </div>
</section>

<section class="why-school block-left2">
    <div class="site-container">
        <div class="section-header text-center">
            <div class="section-subtitle">
                <span>Why</span>
            </div>
            <div class="section-title">
                <h2>Organization Need Asylon</h2>
            </div>
        </div>
        <div class="why-grid">
            <div class="why-bx">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/123Group.png" alt="">
                </div>
                <div class="text-bx">
                    <p>Safe reporting channel for employees (anonymous or named).</p>
                </div>
            </div>



            <div class="why-bx">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/eyVector.png" alt="">
                </div>
                <div class="text-bx">
                    <p>Single system for HR, security, and legal collaboration.</p>
                </div>
            </div>

            <div class="why-bx">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/bxVector.png" alt="">
                </div>
                <div class="text-bx">
                    <p>Clear documentation that supports compliance and audits.</p>
                </div>
            </div>

            <div class="why-bx">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/haVector.png" alt="">
                </div>
                <div class="text-bx">
                    <p>Better early detection of workplace conflict or unsafe behavior.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="report-section block-p2 bg2 block-left">
    <div class="site-container">
        <div class="report-grid align-items-center">



            <div class="report-bx">
                <div class="section-header">

                    <div class="section-subtitle">
                        <span>How It</span>
                    </div>

                    <div class="section-title">
                        <h2>Fits Organizational <br> Processes</h2>
                    </div>

                    <ul class="site-list">
                       <li>Integrates with existing HR and safety workflows.</li>
                       <li>Supports role-based routing for HR, corporate security, and legal.</li>
                       <li>Tracks communication and follow-up steps.</li>
                       <li>Makes policy enforcement more consistent.</li>
                    </ul>

                </div>
            </div>


            <div class="report-bx">
                <div class="right-image">
                    <img src="{{ $assetBase }}/images/99Group.png" alt="">
                </div>
            </div>

        </div>
    </div>
</section>

<section class="example-block block-left2">
    <div class="site-container">
        <div class="section-header text-center">
            <div class="section-title">
                <h2>Example Scenarios</h2>
            </div>
        </div>
        <div class="example-grid">
            <div class="example-card">
                <div class="icon">
                    01
                </div>
                <div class="text-block">
                    <h2>Scenario 1</h2>
                    <p>Employee reports threatening messages from a coworker.</p>
                </div>
            </div>

            <div class="example-card">
                <div class="icon">
                    02
                </div>
                <div class="text-block">
                    <h2>Scenario 2</h2>
                    <p>Anonymous tip about harassment or discrimination.</p>
                </div>
            </div>

            <div class="example-card">
                <div class="icon">
                    03
                </div>
                <div class="text-block">
                    <h2>Scenario 3</h2>
                    <p>Concern about domestic issues spilling into the workplace.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="report-section block-p2 bg2">
    <div class="site-container">
        <div class="report-grid ">

            <div class="report-bx">
                <div class="right-image">
                    <img src="{{ $assetBase }}/images/45Group.png" alt="">
                </div>
            </div>

            <div class="report-bx">
                <div class="section-header">

                    <div class="section-subtitle">
                        <span>THE Outcome</span>
                    </div>

                    <div class="section-title">

                        <h2>Outcomes / Benefits</h2>
                    </div>

                    <ul class="site-list">
                      <li>Reduced risk of workplace incidents</li>
                      <li>Stronger compliance and audit readiness</li>
                      <li>Better HR-security-legal collaboration</li>
                      <li>Faster response to escalating behavior</li>
                      <li>Clear case history for future reference</li>
                    </ul>
                    <div class="action-btn" style="margin-top: 10px;">
                        <a href="{{ route('marketing.demo') }}" class="site-btn-dark">Get Consultation for Your Organization</a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
@endsection
