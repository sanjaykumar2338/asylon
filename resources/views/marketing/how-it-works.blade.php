@extends('marketing.layout')

@section('title', 'Asylon | How It Works')

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>How It Works</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">Home </a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.how') }}">How It Works </a></span>

            </div>

        </div>
    </div>
</section>
<section class="asylon-works">
    <div class="site-container">
        <div class="section-header text-center">
            <div class="section-title">
                <h2>Asylon Works</h2>
            </div>
            <p>Asylon brings every report, action, and outcome into one place so your team can move from scattered
                emails and hallway conversations to a clear, repeatable process.</p>
        </div>

        <div class="asylon-grid">
            <div class="asylon-card">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/img-1.png" alt="">
                </div>
                <div class="card-text-block">
                    <h2>Reporter</h2>
                </div>
            </div>
            <div class="asylon-card">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/img-2.png" alt="">
                </div>
                <div class="card-text-block">
                    <h2>Asylon <br> Platform</h2>
                </div>
            </div>
            <div class="asylon-card">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/img-3.png" alt="">
                </div>
                <div class="card-text-block">
                    <h2>Multidiciplinary <br> Team</h2>
                </div>
            </div>
            <div class="asylon-card">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/img-4.png" alt="">
                </div>
                <div class="card-text-block">
                    <h2>Action Plan</h2>
                </div>
            </div>
            <div class="asylon-card">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/img-5.png" alt="">
                </div>
                <div class="card-text-block">
                    <h2>Follow-Up & <br> Pattern</h2>
                </div>
            </div>

        </div>
        <div class="action-btn text-center">
            <a href="{{ route('marketing.demo') }}" class="site-btn-dark">Book a Live Walkthrough</a>
        </div>
    </div>
</section>

<section class="report-section bg2 right-root">
    <div class="site-container">
        <div class="report-grid">


            <div class="report-bx">
                <div class="section-header">

                    <div class="section-subtitle">
                        <span>Report Submission</span>
                    </div>

                    <div class="section-title">
                        <h2>A Concern Is Reported</h2>
                    </div>



                    <p>Students, staff, members, and employees can share concerns from any device anonymously
                        or by
                        name
                        using a secure web form branded for your organization.</p>



                    <ul class="site-list">
                        <li> Web portal and mobile-friendly forms</li>
                        <li>QR codes posted on campus, hallways, or around your <br> facility</li>
                        <li>Optional file uploads: screenshots, documents, photos</li>
                        <li>Support for short text or longer narrative reports</li>
                    </ul>

                </div>
            </div>


            <div class="report-bx">
                <div class="right-image">
                    <img src="{{ $assetBase }}/images/1-Group.png" alt="">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="report-section report-section2 bg2 block-left">
    <div class="site-container">
        <div class="report-grid">


            <div class="report-bx">
                <div class="right-image">
                    <img src="{{ $assetBase }}/images/g-2.png" alt="" class="align-image">
                </div>
            </div>

            <div class="report-bx">
                <div class="section-header">

                    <div class="section-subtitle">
                        <span>Routing & Notifications</span>
                    </div>

                    <div class="section-title">
                        <h2>The Right People Are Notified</h2>
                    </div>
                    <p>Asylon routes new reports to the right roles with clear permissions <br> and escalation
                        rules.</p>


                    <ul class="site-list">
                        <li>Role-based routing to platform admins, principals, <br> pastors, HR, and safety teams
                        </li>
                        <li>Custom escalation rules for weapons, self-harm, <br> bullying, and more</li>
                        <li>Email (and optionally SMS / in-app) notifications when high-risk <br> reports arrive
                        </li>
                    </ul>

                </div>
            </div>

        </div>
    </div>
</section>

<section class="report-section report-section3 bg2 right-root ">
    <div class="site-container">
        <div class="report-grid">



            <div class="report-bx">
                <div class="section-header">

                    <div class="section-subtitle">
                        <span>Review & Investigation</span>
                    </div>

                    <div class="section-title">
                        <h2>Your Team Reviews and Responds</h2>
                    </div>
                    <p>Each report becomes a case with its own timeline, assignments, and <br> documentation</p>

                    <ul class="site-list">
                        <li>Auto-numbered cases with status (New, In Review, <br> Active, Closed)</li>
                        <li>Assign reviewers and add internal notes and follow-up <br> tasks</li>
                        <li>Log communication with students, families, staff, and law <br> enforcement</li>
                    </ul>

                </div>
            </div>

            <div class="report-bx">
                <div class="right-image">
                    <img src="{{ $assetBase }}/images/3Group.png" alt="">
                </div>
            </div>

        </div>
    </div>
</section>

<section class="report-section report-section2 bg2 block-left">
    <div class="site-container">
        <div class="report-grid">


            <div class="report-bx">
                <div class="right-image">
                    <img src="{{ $assetBase }}/images/4Group.png" alt="">
                </div>
            </div>

            <div class="report-bx">
                <div class="section-header">

                    <div class="section-subtitle">
                        <span>Documentation & Reporting</span>
                    </div>

                    <div class="section-title">
                        <h2>You Document Every Step</h2>
                    </div>
                    <p>Asylon creates a complete record that you can search, export, and <br> share when needed.</p>

                    <ul class="site-list">
                        <li>Search case history by category (bullying, weapons, self- <br> harm, social media, etc.
                        </li>
                        <li>Export summaries for board briefings, audits, or law <br> enforcement packets (PDF/CSV)
                        </li>
                        <li>Trend charts by category, location, and timeframe</li>
                    </ul>

                </div>
            </div>

        </div>
    </div>
</section>

<section class="report-section report-section3 bg1 block-left2">
    <div class="site-container">
        <div class="report-grid">



            <div class="report-bx">
                <div class="section-header">

                    <div class="section-subtitle">
                        <span>Privacy & Anonymity</span>
                    </div>

                    <div class="section-title">
                        <h2>Privacy, Anonymity, and <br> Data Security</h2>
                    </div>

                    <ul class="site-list">
                        <li>Anonymous reporting options so people can speak up <br> without fear.</li>
                        <li>Role-based access so only the right leaders can view <br> sensitive details.</li>
                        <li>Data encrypted in transit and at rest, with secure hosting and <br> regular backups.
                        </li>
                    </ul>



                    <div class="action-btn2">
                        <a href="{{ route('marketing.demo') }}" class="site-btn-dark">Walk Through a Live Case Flow</a>
                    </div>

                </div>
            </div>

            <div class="report-bx">
                <div class="right-image">
                    <img src="{{ $assetBase }}/images/5Group.png" alt="">
                </div>
            </div>

        </div>
    </div>
</section>

<section class="cta-section inner-pages cta-w1">
    <div class="site-container">
        <img src="{{ $assetBase }}/images/Vectore.png" alt="Bird Icon" class="cta-icon">
        <div class="section-title">
            <h2>Ready to see how Asylon works with your <span> team's real-world scenarios? </span></h2>
        </div>
        <div class="action-btn text-center">
            <a href="{{ route('marketing.demo') }}" class="site-btn-dark">Book a Demo</a>
        </div>
    </div>
</section>
@endsection
