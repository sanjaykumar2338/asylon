@extends('marketing.layout')

@section('title', __('marketing.titles.how_it_works'))

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>{{ __('frontend.nav.how_it_works') }}</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">{{ __('frontend.nav.home') }} </a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.how') }}">{{ __('frontend.nav.how_it_works') }} </a></span>

            </div>

        </div>
    </div>
</section>
<section class="asylon-works">
    <div class="site-container">
        <div class="section-header text-center">
            <div class="section-title">
                <h2>{{ __('marketing.how_it_works.hero_title') }}</h2>
            </div>
            <p>{{ __('marketing.how_it_works.hero_body') }}</p>
        </div>

        <div class="asylon-grid">
            <div class="asylon-card">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/img-1.png" alt="">
                </div>
                <div class="card-text-block">
                    <h2>{{ __('marketing.how_it_works.flow.reporter') }}</h2>
                </div>
            </div>
            <div class="asylon-card">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/img-2.png" alt="">
                </div>
                <div class="card-text-block">
                    <h2>{!! __('marketing.how_it_works.flow.platform') !!}</h2>
                </div>
            </div>
            <div class="asylon-card">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/img-3.png" alt="">
                </div>
                <div class="card-text-block">
                    <h2>{!! __('marketing.how_it_works.flow.team') !!}</h2>
                </div>
            </div>
            <div class="asylon-card">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/img-4.png" alt="">
                </div>
                <div class="card-text-block">
                    <h2>{{ __('marketing.how_it_works.flow.action_plan') }}</h2>
                </div>
            </div>
            <div class="asylon-card">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/img-5.png" alt="">
                </div>
                <div class="card-text-block">
                    <h2>{!! __('marketing.how_it_works.flow.follow_up') !!}</h2>
                </div>
            </div>

        </div>
        <div class="action-btn text-center">
            <a href="{{ route('marketing.demo') }}" class="site-btn-dark">{{ __('frontend.cta.book_live_walkthrough') }}</a>
        </div>
    </div>
</section>

<section class="report-section bg2 right-root">
    <div class="site-container">
        <div class="report-grid">


            <div class="report-bx">
                <div class="section-header">

                    <div class="section-subtitle">
                        <span>{{ __('marketing.how_it_works.sections.report_submission.label') }}</span>
                    </div>

                    <div class="section-title">
                        <h2>{{ __('marketing.how_it_works.sections.report_submission.title') }}</h2>
                    </div>



                    <p>{{ __('marketing.how_it_works.sections.report_submission.body') }}</p>



                    <ul class="site-list">
                        <li>{{ __('marketing.how_it_works.sections.report_submission.item_1') }}</li>
                        <li>{!! __('marketing.how_it_works.sections.report_submission.item_2') !!}</li>
                        <li>{{ __('marketing.how_it_works.sections.report_submission.item_3') }}</li>
                        <li>{{ __('marketing.how_it_works.sections.report_submission.item_4') }}</li>
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
                        <span>{{ __('marketing.how_it_works.sections.routing.label') }}</span>
                    </div>

                    <div class="section-title">
                        <h2>{{ __('marketing.how_it_works.sections.routing.title') }}</h2>
                    </div>
                    <p>{!! __('marketing.how_it_works.sections.routing.body') !!}</p>


                    <ul class="site-list">
                        <li>{!! __('marketing.how_it_works.sections.routing.item_1') !!}</li>
                        <li>{!! __('marketing.how_it_works.sections.routing.item_2') !!}</li>
                        <li>{!! __('marketing.how_it_works.sections.routing.item_3') !!}</li>
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
                        <span>{{ __('marketing.how_it_works.sections.review.label') }}</span>
                    </div>

                    <div class="section-title">
                        <h2>{{ __('marketing.how_it_works.sections.review.title') }}</h2>
                    </div>
                    <p>{!! __('marketing.how_it_works.sections.review.body') !!}</p>

                    <ul class="site-list">
                        <li>{!! __('marketing.how_it_works.sections.review.item_1') !!}</li>
                        <li>{!! __('marketing.how_it_works.sections.review.item_2') !!}</li>
                        <li>{!! __('marketing.how_it_works.sections.review.item_3') !!}</li>
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
                        <span>{{ __('marketing.how_it_works.sections.documentation.label') }}</span>
                    </div>

                    <div class="section-title">
                        <h2>{{ __('marketing.how_it_works.sections.documentation.title') }}</h2>
                    </div>
                    <p>{!! __('marketing.how_it_works.sections.documentation.body') !!}</p>

                    <ul class="site-list">
                        <li>{!! __('marketing.how_it_works.sections.documentation.item_1') !!}</li>
                        <li>{!! __('marketing.how_it_works.sections.documentation.item_2') !!}</li>
                        <li>{{ __('marketing.how_it_works.sections.documentation.item_3') }}</li>
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
                        <span>{{ __('marketing.how_it_works.sections.privacy.label') }}</span>
                    </div>

                    <div class="section-title">
                        <h2>{!! __('marketing.how_it_works.sections.privacy.title') !!}</h2>
                    </div>

                    <ul class="site-list">
                        <li>{!! __('marketing.how_it_works.sections.privacy.item_1') !!}</li>
                        <li>{!! __('marketing.how_it_works.sections.privacy.item_2') !!}</li>
                        <li>{!! __('marketing.how_it_works.sections.privacy.item_3') !!}</li>
                    </ul>



                    <div class="action-btn2">
                        <a href="{{ route('marketing.demo') }}" class="site-btn-dark">{{ __('frontend.cta.walk_through_case') }}</a>
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
        <img src="{{ $assetBase }}/images/Vectore.png" alt="{{ __('frontend.home.cta_icon_alt') }}" class="cta-icon">
        <div class="section-title">
            <h2>{!! __('marketing.how_it_works.cta_title') !!}</h2>
        </div>
        <div class="action-btn text-center">
            <a href="{{ route('marketing.demo') }}" class="site-btn-dark">{{ __('frontend.cta.book_demo') }}</a>
        </div>
    </div>
</section>
@endsection
