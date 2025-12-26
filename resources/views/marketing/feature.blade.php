@extends('marketing.layout')

@section('title', 'Asylon | Features')

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>Feature</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">Home </a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.feature') }}">Feature</a></span>

            </div>

        </div>
    </div>
</section>

<section class="Feature-Block block-left2">
    <div class="site-container">

        <div class="section-head text-center">

            <div class="section-subtitle">
                <span>Platform</span>
            </div>
            <div class="section-title">
                <h2>Platform Features at a Glance</h2>
            </div>
            <p>Everything you need to capture concerns, coordinate your response, and document every step.</p>
        </div>
        <div class="Feature-Grid">


            <div class="Feature-Card">
                <div class="card-head">
                    <div class="icon">
                        <img src="{{ $assetBase }}/images/!Vector.png" alt="">
                    </div>
                    <div class="card-title">
                        <h2>Reporting Portal</h2>
                    </div>
                </div>
                <div class="card-text">
                    <ul>

                        <li>Custom branding with your logo and colors</li>
                        <li>Web and mobile-friendly report forms</li>
                        <li>Support for text, file uploads, and attachments</li>
                        <li>Optional multi-language support (as available)</li>
                    </ul>
                </div>
            </div>

            <div class="Feature-Card">
                <div class="card-head">
                    <div class="icon">
                        <img src="{{ $assetBase }}/images/uVector.png" alt="">
                    </div>
                    <div class="card-title">
                        <h2>Role-Based Access</h2>
                    </div>
                </div>
                <div class="card-text">
                    <ul>

                        <li>Platform Admin: configuration, user management, and global reporting</li>
                        <li>Executive Admin: principals, pastors, or directors with decision-making <br> authority</li>
                        <li>Reviewers: counselors, safety, HR, or designated staff</li>
                    </ul>
                </div>
            </div>

            <div class="Feature-Card">
                <div class="card-head">
                    <div class="icon">
                        <img src="{{ $assetBase }}/images/sVector.png" alt="">
                    </div>
                    <div class="card-title">
                        <h2>Case Management</h2>
                    </div>
                </div>
                <div class="card-text">
                    <ul>
                        <li>Auto-numbered cases with clear  <br> status (New, In Review, Active, <br> Closed)</li>
                        <li>Notes, tasks, due dates, and <br> attachments in one place</li>
                        <li>Timeline of every action taken on a <br> case
                         </li>
                    </ul>
                </div>
            </div>

            <div class="Feature-Card">
                <div class="card-head">
                    <div class="icon">
                        <img src="{{ $assetBase }}/images/b1Vector.png" alt="">
                    </div>
                    <div class="card-title">
                        <h2>Notifications & Escalation</h2>
                    </div>
                </div>
                <div class="card-text">
                    <ul>
                        <li>Email (and optional SMS) alerts for <br> new and high-risk cases</li>
                        <li>Escalation rules for weapons, self- <br> harm, and other critical categories</li>


                        <li>Time-based reminders for overdue follow-ups</li>
                    </ul>
                </div>
            </div>

            <div class="Feature-Card">
                <div class="card-head">
                    <div class="icon">
                        <img src="{{ $assetBase }}/images/g1Vector.png" alt="">
                    </div>
                    <div class="card-title">
                        <h2>Analytics & Reporting</h2>
                    </div>
                </div>
                <div class="card-text">
                    <ul>
                        <li>Dashboards showing incidents by category, campus, and time</li>
                        <li>Exports (PDF/CSV) for leadership, <br> board packets, and law <br>
                            enforcement</li>
                    </ul>
                </div>
            </div>

            <div class="Feature-Card">
                <div class="card-head">
                    <div class="icon">
                        <img src="{{ $assetBase }}/images/se.png" alt="">
                    </div>
                    <div class="card-title">
                        <h2>Security & Compliance</h2>
                    </div>
                </div>
                <div class="card-text">
                    <ul>
                        <li>SSL/HTTPS encryption for all traffic</li>
                        <li>Data encrypted at rest and in transit</li>
                        <li>Secure hosting, access controls, and regular backups</li>
                        <li>Aligned with FERPA and relevant <br> privacy best practices (adjust based <br> on actual
                            compliance)</li>
                    </ul>
                </div>
            </div>

        </div>
        <div class="root-btn text-center" >
            <a href="{{ route('marketing.demo') }}" class="site-btn-dark">Schedule a Consultation</a>
        </div>
    </div>
</section>
@endsection
