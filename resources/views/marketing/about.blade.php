@extends('marketing.layout')

@section('title', 'Asylon | About')

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>About</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">Home </a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.about') }}">About</a></span>

            </div>

        </div>
    </div>
</section>

<section class="story-block block-left block-left-2" style="">
    <div class="site-container">
        <div class="about-grid">
            <div class="about-bx">
                <div class="section-subtitle">
                    <span>Our Story</span>
                </div>
                <div class="section-title">
                    <h2>Why We Built Asylon</h2>
                </div>
                <div class="text-bx">
                    <p>After years in law enforcement and seeing the aftermath of school and church incidents, we
                        kept seeing the same pattern: warning signs were there, but they were scattered,
                        undocumented, or never shared at all. Asylon was created to give people a safe way to speak
                        up and to give leaders a clear way to respond before a crisis.</p>
                </div>
            </div>
            <div class="about-bx">
                <div class="image-bx">
                    <img src="{{ $assetBase }}/images/site-about.png" alt="">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="Mission-Block">
    <div class="container">
        <div class="mission-grid">
            <div class="mission-card">
                <div class="card-icon">
                    <img src="{{ $assetBase }}/images/gGroup.png" alt="">
                </div>
                <div class="card-text-root">
                    <h4>Our Mission</h4>
                    <p>Our mission is to help schools, churches, <br> and organizations catch warning signs <br>
                        earlier and
                        respond with care, clarity, <br> and accountability.</p>
                </div>
            </div>
            <div class="mission-card">
                <div class="card-icon">
                    <img src="{{ $assetBase }}/images/rGroup.png" alt="">
                </div>
                <div class="card-text-root">
                    <h4>Our Vision</h4>
                    <p>We envision communities where <br> safety isn't built on fear or rumor, but <br> on trusted
                        channels,
                        trained teams, <br> and documented action.</p>

                </div>
            </div>

        </div>
    </div>


    <div class="section-image-bx">
        <img src="{{ $assetBase }}/images/section-sp.png" alt="">
    </div>
</section>

<section class="randy-root">
    <div class="container">
        <div class="Randy-grid">
            <div class="image-bx">
                <img src="{{ $assetBase }}/images/mGroup.png" alt="">
            </div>
            <div class="right-text-bx">

                <div class="section-title">
                    <h2>Randy Mays</h2>
                    <span>Veteran law enforcement officer</span>
                </div>
                <p>"Randy Mays is a veteran law enforcement officer and safety practitioner who has spent years
                    responding to critical incidents and working with schools and churches to improve prevention."</p>
            </div>
        </div>
    </div>
</section>

<section class="partners-section">
    <div class="site-container2 ">
        <div class="section-header text-center">
            <div class="section-title">
                <h2>Our partners</h2>
            </div>
        </div>

        <div class="marqu-slider">
            <div class="logo-bx"><img src="{{ $assetBase }}/images/image-03.png" alt=""></div>
            <div class="logo-bx"><img src="{{ $assetBase }}/images/image-6.png" alt=""></div>
            <div class="logo-bx"><img src="{{ $assetBase }}/images/image7.png" alt=""></div>
            <div class="logo-bx"><img src="{{ $assetBase }}/images/image7.png" alt=""></div>
            <div class="logo-bx"><img src="{{ $assetBase }}/images/image-02.png" alt=""></div>
            <div class="logo-bx"><img src="{{ $assetBase }}/images/image-02.png" alt=""></div>

            <div class="logo-bx"><img src="{{ $assetBase }}/images/image-03.png" alt=""></div>
            <div class="logo-bx"><img src="{{ $assetBase }}/images/image-6.png" alt=""></div>
            <div class="logo-bx"><img src="{{ $assetBase }}/images/image7.png" alt=""></div>
            <div class="logo-bx"><img src="{{ $assetBase }}/images/image7.png" alt=""></div>
            <div class="logo-bx"><img src="{{ $assetBase }}/images/image-02.png" alt=""></div>
            <div class="logo-bx"><img src="{{ $assetBase }}/images/image-02.png" alt=""></div>
        </div>
    </div>
</section>
@endsection
