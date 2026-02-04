@extends('marketing.layout')

@section('title', __('marketing.titles.about'))

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>{{ __('frontend.nav.about') }}</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">{{ __('frontend.nav.home') }} </a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.about') }}">{{ __('frontend.nav.about') }}</a></span>

            </div>

        </div>
    </div>
</section>

<section class="story-block block-left block-left-2" style="">
    <div class="site-container">
        <div class="about-grid">
            <div class="about-bx">
                <div class="section-subtitle">
                    <span>{{ __('marketing.about.story_label') }}</span>
                </div>
                <div class="section-title">
                    <h2>{{ __('marketing.about.story_title') }}</h2>
                </div>
                <div class="text-bx">
                    <p>{{ __('marketing.about.story_body') }}</p>
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
                    <h4>{{ __('marketing.about.mission_title') }}</h4>
                    <p>{!! __('marketing.about.mission_body') !!}</p>
                </div>
            </div>
            <div class="mission-card">
                <div class="card-icon">
                    <img src="{{ $assetBase }}/images/rGroup.png" alt="">
                </div>
                <div class="card-text-root">
                    <h4>{{ __('marketing.about.vision_title') }}</h4>
                    <p>{!! __('marketing.about.vision_body') !!}</p>

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
                <img
                    src="{{ asset('assets/images/founder.jpeg') }}"
                    alt="{{ __('marketing.about.founder_photo_alt') }}"
                    class="founder-photo"
                    loading="lazy"
                >
            </div>
            <div class="right-text-bx">

                <div class="section-title">
                    <h2>{{ __('marketing.about.founder_name') }}</h2>
                    <span>{{ __('marketing.about.founder_role') }}</span>
                </div>
                <p>{{ __('marketing.about.founder_quote') }}</p>
            </div>
        </div>
    </div>
</section>

<section class="partners-section">
    <div class="site-container2 ">
        <div class="section-header text-center">
            <div class="section-title">
                <h2>{{ __('frontend.home.partners_title') }}</h2>
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
