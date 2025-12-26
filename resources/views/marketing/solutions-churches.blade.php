@extends('marketing.layout')

@section('title', 'Asylon | Solutions - Churches')

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
                <span><a href="{{ route('marketing.solutions.churches') }}">Solutions</a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.solutions.churches') }}">Churches</a></span>

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
                        <span>Church</span>
                    </div>

                    <div class="section-title">
                        <h2>Give Your Church a Safe Way <br> to Share Concerns</h2>
                    </div>
                    <p>Students and staff often see early warning signs long before an <br> incident occurs. Asylon
                        gives
                        them a safe place to speak up - and <br> gives your safety team a clear way to review,
                        track, and
                        act on concerns.</p>

                    <div class="action-btn">
                        <a href="{{ route('marketing.demo') }}" class="site-btn-dark">Schedule a Church Safety Consultation</a>
                    </div>
                </div>
            </div>


            <div class="report-bx">
                <div class="right-image">
                    <img src="{{ $assetBase }}/images/11Group.png" alt="">
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
                    <img src="{{ $assetBase }}/images/112Group.png" alt="">
                </div>
            </div>


            <div class="report-bx">
                <div class="section-header">

                    <div class="section-subtitle">
                        <span>THE PROBLEM</span>
                    </div>

                    <div class="section-title">
                        <h2>Real Challenges Churches <br> Faces</h2>
                    </div>

                    <ul class="site-list">

                        <li>Open campuses where anyone can walk in</li>
                        <li>Domestic conflicts and restraining orders that may spill over into services</li>
                        <li>People posting disturbing content online but never saying it in <br> person</li>
                        <li>Volunteers feeling uneasy about someone's behavior and not knowing where to turn</li>
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
                <h2>Churches Need Asylon</h2>
            </div>
        </div>
        <div class="why-grid">
            <div class="why-bx">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/123Group.png" alt="">
                </div>
                <div class="text-bx">
                    <p>Provides a respectful, discreet reporting channel.</p>
                </div>
            </div>



            <div class="why-bx">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/eyVector.png" alt="">
                </div>
                <div class="text-bx">
                    <p>Gives pastors and safety leaders a central place to track concerns.</p>
                </div>
            </div>

            <div class="why-bx">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/bxVector.png" alt="">
                </div>
                <div class="text-bx">
                    <p>Ensures patterns or early warning signs don't go unnoticed.</p>
                </div>
            </div>

            <div class="why-bx">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/haVector.png" alt="">
                </div>
                <div class="text-bx">
                    <p>Supports caring follow-up and community health initiatives.</p>
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
                        <h2>Fits Church Processes</h2>
                    </div>

                    <ul class="site-list">
                        <li>Works with pastoral care + safety team structures.</li>
                        <li>Helps coordinate communication across staff and volunteer teams.</li>
                        <li>Secure enough for sensitive personal information.</li>
                        <li>Encourages proactive, compassionate intervention.</li>
                    </ul>

                </div>
            </div>


            <div class="report-bx">
                <div class="right-image">
                    <img src="{{ $assetBase }}/images/111Group.png" alt="">
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
                    <p>Member shares worrying social media posts from someone in the congregation.</p>
                </div>
            </div>

            <div class="example-card">
                <div class="icon">
                    02
                </div>
                <div class="text-block">
                    <h2>Scenario 2</h2>
                    <p>Volunteer reports feeling unsafe around a particular individual.</p>
                </div>
            </div>

            <div class="example-card">
                <div class="icon">
                    03
                </div>
                <div class="text-block">
                    <h2>Scenario 3</h2>
                    <p>Staff member wants to flag a potential domestic spillover situation.</p>
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
                    <img src="{{ $assetBase }}/images/212Group.png" alt="">
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
                        <li>Stronger awareness of safety risks</li>
                        <li>More informed pastoral care decisions</li>
                        <li>Easier coordination across volunteers and staff</li>
                        <li>Clear documentation for follow-up</li>
                        <li>A safer, more supportive environment</li>
                    </ul>
                    <div class="action-btn" style="margin-top: 10px;">
                        <a href="{{ route('marketing.demo') }}" class="site-btn-dark">Get Consultation for Your Church</a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
@endsection
