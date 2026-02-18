@extends('marketing.layout')

@section('title', __('frontend.page_titles.home'))

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="hero-section">
    <div class="site-container">
        <div class="hero-grid">
            <div class="hero-content">
                <div class="hero-title">
                    <h2>{!! __('frontend.home.hero_title') !!}</h2>
                </div>
                <p>
                    {{ __('frontend.home.hero_desc') }}
                </p>

                <div class="hero-actions">
                    <a href="{{ route('marketing.demo') }}" class="site-btn-dark">{{ __('frontend.cta.schedule_call') }}</a>
                    <a href="{{ route('marketing.resources') }}" class="site-btn-light">{{ __('frontend.cta.watch_overview') }}</a>
                </div>
            </div>

            <div class="hero-image">
                <img src="{{ $assetBase }}/images/h1.png" alt="{{ __('frontend.home.hero_image_alt') }}" class="pc-image">
                <img src="{{ $assetBase }}/images/Dashboard-as-1.png" alt="{{ __('frontend.home.hero_image_alt') }}" class="mobile-image">

            </div>
        </div>
    </div>
    </section>

    <section class="who-we-serve">
        <div class="site-container">

            <div class="section-header text-center">
                <div class="section-subtitle">
                    <span>{{ __('frontend.home.who_we_serve_label') }}</span>
                </div>
                <div class="section-title">
                    <h2>{{ __('frontend.home.who_we_serve_title') }}</h2>
                </div>
            </div>
            <div class="serve-grid">
                <div class="serve-card">
                    <div class="card-icon">
                        <img src="{{ $assetBase }}/images/Group.png" alt="{{ __('frontend.home.serve.schools.title') }}">
                    </div>
                    <h3>{{ __('frontend.home.serve.schools.title') }}</h3>
                    <p>
                        {{ __('frontend.home.serve.schools.desc') }}
                    </p>
                    <a href="{{ route('marketing.solutions.schools') }}" class="site-btn-light">{{ __('frontend.home.serve.schools.cta') }}</a>
                </div>

                <div class="serve-card">
                    <div class="card-icon">
                        <img src="{{ $assetBase }}/images/Vector.png" alt="{{ __('frontend.home.serve.churches.title') }}">
                    </div>
                    <h3>{{ __('frontend.home.serve.churches.title') }}</h3>
                    <p>
                        {{ __('frontend.home.serve.churches.desc') }}
                    </p>
                    <a href="{{ route('marketing.solutions.churches') }}" class="site-btn-light">{{ __('frontend.home.serve.churches.cta') }}</a>
                </div>

                <div class="serve-card">
                    <div class="card-icon">
                        <img src="{{ $assetBase }}/images/Vector2.png" alt="{{ __('frontend.home.serve.organizations.title') }}">
                    </div>
                    <h3>{{ __('frontend.home.serve.organizations.title') }}</h3>
                    <p>
                        {{ __('frontend.home.serve.organizations.desc') }}
                    </p>
                    <a href="{{ route('marketing.solutions.organizations') }}" class="site-btn-light">{{ __('frontend.home.serve.organizations.cta') }}</a>
                </div>
            </div>

        </div>
    </section>

    <section class="how-it-works">
        <div class="site-container">

            <div class="section-header text-center">
                <div class="section-subtitle">
                    <span>{{ __('frontend.home.how_it_works_label') }}</span>
                </div>
                <div class="section-title">
                    <h2>{!! __('frontend.home.how_it_works_title') !!}</h2>
                </div>
            </div>
            <div class="steps-wrapper">

                <div class="step">
                    <div class="step-number">01</div>
                    <div class="step-content">
                        <h3>{{ __('frontend.home.how_steps.step1.title') }}</h3>
                        <p>{{ __('frontend.home.how_steps.step1.desc') }}</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">02</div>
                    <div class="step-content">
                        <h3>{{ __('frontend.home.how_steps.step2.title') }}</h3>
                        <p>{{ __('frontend.home.how_steps.step2.desc') }}</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">03</div>
                    <div class="step-content">
                        <h3>{{ __('frontend.home.how_steps.step3.title') }}</h3>
                        <p>{{ __('frontend.home.how_steps.step3.desc') }}</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">04</div>
                    <div class="step-content">
                        <h3>{{ __('frontend.home.how_steps.step4.title') }}</h3>
                        <p>{{ __('frontend.home.how_steps.step4.desc') }}</p>
                    </div>
                </div>

            </div>
            <div class="action-btn text-center">
                <a href="{{ route('marketing.how') }}" class="site-btn-dark">{{ __('frontend.cta.see_workflow') }}</a>
            </div>

        </div>
    </section>

    <section class="why-asylon">
        <div class="site-container">

            <div class="asylon-grid">
                <div class="column">

                    <div class="section-header ">
                        <div class="section-subtitle">
                            <span>{{ __('frontend.home.why_label') }}</span>
                        </div>
                        <div class="section-title">
                            <h2>{{ __('frontend.home.why_title') }}</h2>
                        </div>
                    </div>

                    <ul class="site-list">
                        <li>{{ __('frontend.home.why_list.item1') }}</li>
                        <li>{{ __('frontend.home.why_list.item2') }}</li>
                        <li>{{ __('frontend.home.why_list.item3') }}</li>
                        <li>{{ __('frontend.home.why_list.item4') }}</li>
                        <li>{{ __('frontend.home.why_list.item5') }}</li>
                    </ul>

                </div>
                <div class="column">
                    <img src="{{ $assetBase }}/images/Rectangle 3463506.png" alt="">
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials-block">
        <div class="site-container">

            <div class="section-header text-center">
                <div class="section-subtitle">
                    <span>{{ __('frontend.home.testimonials.label') }}</span>
                </div>
                <div class="section-title">
                    <h2>{!! __('frontend.home.testimonials.title') !!}</h2>
                </div>
            </div>

            <div class="testimonials-slider">
                <div class="testimonial-card">
                    <p>{{ __('frontend.home.testimonials.items.0.quote') }}</p>
                    <div class="testimonial-author">
                        <div class="main-bx">
                            <div class="author-info">
                                <strong>{{ __('frontend.home.testimonials.items.0.author') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <p>{{ __('frontend.home.testimonials.items.1.quote') }}</p>
                    <div class="testimonial-author">
                        <div class="main-bx">
                            <div class="author-info">
                                <strong>{{ __('frontend.home.testimonials.items.1.author') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <p>{{ __('frontend.home.testimonials.items.2.quote') }}</p>
                    <div class="testimonial-author">
                        <div class="main-bx">
                            <div class="author-info">
                                <strong>{{ __('frontend.home.testimonials.items.2.author') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <p>{{ __('frontend.home.testimonials.items.3.quote') }}</p>
                    <div class="testimonial-author">
                        <div class="main-bx">
                            <div class="author-info">
                                <strong>{{ __('frontend.home.testimonials.items.3.author') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <p>{{ __('frontend.home.testimonials.items.4.quote') }}</p>
                    <div class="testimonial-author">
                        <div class="main-bx">
                            <div class="author-info">
                                <strong>{{ __('frontend.home.testimonials.items.4.author') }}</strong>
                            </div>
                        </div>
                    </div>
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

            <div class="partners-placeholder">
                <div class="partner-card">
                    <span>{{ __('frontend.home.partners.0') }}</span>
                </div>
                <div class="partner-card">
                    <span>{{ __('frontend.home.partners.1') }}</span>
                </div>
                <div class="partner-card">
                    <span>{{ __('frontend.home.partners.2') }}</span>
                </div>
                <div class="partner-card">
                    <span>{{ __('frontend.home.partners.3') }}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="site-container">
            <img src="{{ $assetBase }}/images/Vectore.png" alt="{{ __('frontend.home.cta_icon_alt') }}" class="cta-icon">
            <div class="section-title">
                <h2>{!! __('frontend.home.cta_title') !!}</h2>
            </div>
            <div class="action-btn text-center">
                <a href="{{ route('marketing.demo') }}" class="site-btn-dark">{{ __('frontend.cta.schedule_consultation') }}</a>
            </div>
        </div>
    </section>
@endsection
