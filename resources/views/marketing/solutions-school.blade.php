@extends('marketing.layout')

@section('title', 'Asylon | Solutions - School')

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
                <span><a href="{{ route('marketing.solutions.schools') }}">Solutions </a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.solutions.schools') }}">Schools </a></span>

            </div>

        </div>
    </div>
</section>

<section class="report-section block-p cta-w1">
    <div class="site-container">
        <div class="report-grid">


            <div class="report-bx">
                <div class="section-header">

                    <div class="section-subtitle">
                        <span>School</span>
                    </div>

                    <div class="section-title">
                        <h2>A Reporting and Threat- <br> Assessment Platform Built <br> for K-12 Safety </h2>
                    </div>


                    <p>Students and staff often see early warning signs long before an <br> incident occurs. Asylon
                        gives
                        them a safe place to speak up - and <br> gives your safety team a clear way to review,
                        track, and
                        act on <br> concerns.</p>


                    <div class="action-btn">
                        <a href="{{ route('marketing.demo') }}" class="site-btn-dark">Schedule a School Safety Consultation</a>
                    </div>
                </div>
            </div>


            <div class="report-bx">
                <div class="right-image">
                    <img src="{{ $assetBase }}/images/so-1.png" alt="">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="report-section block-p2 bg2 block-left">
    <div class="site-container">
        <div class="report-grid">

            <div class="report-bx">
                <div class="right-image">
                    <img src="{{ $assetBase }}/images/so-2.png" alt="">
                </div>
            </div>


            <div class="report-bx">
                <div class="section-header">

                    <div class="section-subtitle">
                        <span>THE PROBLEM</span>
                    </div>

                    <div class="section-title">
                        <h2>The Safety Challenges <br> Schools Deal With Every Day</h2>
                    </div>

                    <ul class="site-list">

                        <li>Violence and weapons talk that students are afraid to report</li>
                        <li> Bullying, cyberbullying, and social media threats</li>
                        <li> Students talking about self-harm or not wanting to live</li>
                        <li> Fentanyl, vaping, and drug activity on and off campus</li>
                        <li> Teachers unsure where to document repeated behavior <br> concerns</li>
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
                <h2>Schools Need Asylon</h2>
            </div>
        </div>
        <div class="why-grid">
            <div class="why-bx">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/userVector.png" alt="">
                </div>
                <div class="text-bx">
                    <p>Gives students and staff a safe, confidential place to speak up.</p>
                </div>
            </div>



            <div class="why-bx">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/eyVector.png" alt="">
                </div>
                <div class="text-bx">
                    <p>Gives principals, counselors, and safety teams a clear workflow to review concerns.</p>
                </div>
            </div>

            <div class="why-bx">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/bxVector.png" alt="">
                </div>
                <div class="text-bx">
                    <p>Connects scattered information into a single, documented case record.</p>
                </div>
            </div>

            <div class="why-bx">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/haVector.png" alt="">
                </div>
                <div class="text-bx">
                    <p>Helps districts spot trends across campuses.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="report-section block-p2 bg2 block-left2">
    <div class="site-container">
        <div class="report-grid align-items-center">



            <div class="report-bx">
                <div class="section-header">

                    <div class="section-subtitle">
                        <span>How It</span>
                    </div>

                    <div class="section-title">

                        <h2>Fits School Processes</h2>
                    </div>

                    <ul class="site-list">
                        <li>Works with existing threat-assessment teams.</li>
                        <li>Supports counselor documentation and follow-up tasks</li>
                        <li>Provides clear routing to principals, SROs, district safety officers.</li>
                        <li>Built for FERPA-aware environments.</li>
                    </ul>

                </div>
            </div>
            <div class="report-bx">
                <div class="right-image">
                    <img src="{{ $assetBase }}/images/so-3.png" alt="">
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
                    <p>Student reports a classmate talking about not wanting to live.</p>
                </div>
            </div>

            <div class="example-card">
                <div class="icon">
                    02
                </div>
                <div class="text-block">
                    <h2>Scenario 2</h2>
                    <p>Screenshot of a social media threat is shared with a counselor.</p>
                </div>
            </div>

            <div class="example-card">
                <div class="icon">
                    03
                </div>
                <div class="text-block">
                    <h2>Scenario 3</h2>
                    <p>Teacher notices escalating behavior but doesn't know whom to tell.</p>
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
                    <img src="{{ $assetBase }}/images/12Group.png" alt="">
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
                        <li>Earlier intervention for at-risk students</li>
                        <li>Better communication between staff and safety teams</li>
                        <li>Unified threat-assessment documentation</li>
                        <li>Reporting consistency across campuses</li>
                        <li>Stronger prevention planning through pattern analysis</li>
                    </ul>
                    <div class="action-btn" style="margin-top: 10px;">
                        <a href="{{ route('marketing.demo') }}" class="site-btn-dark">Get Consultation for Your School</a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
@endsection
