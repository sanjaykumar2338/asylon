@extends('marketing.layout')

@section('title', __('marketing.titles.solutions_organization'))

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>{{ __('frontend.nav.solutions') }}</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">{{ __('frontend.nav.home') }} </a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.solutions.organizations') }}">{{ __('frontend.nav.solutions') }}</a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.solutions.organizations') }}">{{ __('frontend.nav.solutions_organizations') }}</a></span>

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
                        <span>{{ __('marketing.solutions.organization.hero_label') }}</span>
                    </div>

                    <div class="section-title">
                        <h2>{!! __('marketing.solutions.organization.hero_title') !!}</h2>
                    </div>
                    <p>{{ __('marketing.solutions.organization.hero_body') }}</p>

                    <div class="action-btn">
                        <a href="{{ route('marketing.demo') }}" class="site-btn-dark">{{ __('frontend.cta.schedule_org_consultation') }}</a>
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
                        <span>{{ __('marketing.solutions.organization.problem_label') }}</span>
                    </div>

                    <div class="section-title">
                        <h2>{!! __('marketing.solutions.organization.problem_title') !!}</h2>
                    </div>

                    <ul class="site-list">
                        <li>{{ __('marketing.solutions.organization.problem_item_1') }}</li>
                        <li>{{ __('marketing.solutions.organization.problem_item_2') }}</li>
                        <li>{{ __('marketing.solutions.organization.problem_item_3') }}</li>
                        <li>{{ __('marketing.solutions.organization.problem_item_4') }}</li>
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
                <span>{{ __('marketing.solutions.organization.why_label') }}</span>
            </div>
            <div class="section-title">
                <h2>{{ __('marketing.solutions.organization.why_title') }}</h2>
            </div>
        </div>
        <div class="why-grid">
            <div class="why-bx">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/123Group.png" alt="">
                </div>
                <div class="text-bx">
                    <p>{{ __('marketing.solutions.organization.why_item_1') }}</p>
                </div>
            </div>



            <div class="why-bx">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/eyVector.png" alt="">
                </div>
                <div class="text-bx">
                    <p>{{ __('marketing.solutions.organization.why_item_2') }}</p>
                </div>
            </div>

            <div class="why-bx">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/bxVector.png" alt="">
                </div>
                <div class="text-bx">
                    <p>{{ __('marketing.solutions.organization.why_item_3') }}</p>
                </div>
            </div>

            <div class="why-bx">
                <div class="icon">
                    <img src="{{ $assetBase }}/images/haVector.png" alt="">
                </div>
                <div class="text-bx">
                    <p>{{ __('marketing.solutions.organization.why_item_4') }}</p>
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
                        <span>{{ __('marketing.solutions.organization.process_label') }}</span>
                    </div>

                    <div class="section-title">
                        <h2>{!! __('marketing.solutions.organization.process_title') !!}</h2>
                    </div>

                    <ul class="site-list">
                       <li>{{ __('marketing.solutions.organization.process_item_1') }}</li>
                       <li>{{ __('marketing.solutions.organization.process_item_2') }}</li>
                       <li>{{ __('marketing.solutions.organization.process_item_3') }}</li>
                       <li>{{ __('marketing.solutions.organization.process_item_4') }}</li>
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
                <h2>{{ __('marketing.solutions.organization.examples_title') }}</h2>
            </div>
        </div>
        <div class="example-grid">
            <div class="example-card">
                <div class="icon">
                    01
                </div>
                <div class="text-block">
                    <h2>{{ __('marketing.solutions.organization.example_1_title') }}</h2>
                    <p>{{ __('marketing.solutions.organization.example_1_body') }}</p>
                </div>
            </div>

            <div class="example-card">
                <div class="icon">
                    02
                </div>
                <div class="text-block">
                    <h2>{{ __('marketing.solutions.organization.example_2_title') }}</h2>
                    <p>{{ __('marketing.solutions.organization.example_2_body') }}</p>
                </div>
            </div>

            <div class="example-card">
                <div class="icon">
                    03
                </div>
                <div class="text-block">
                    <h2>{{ __('marketing.solutions.organization.example_3_title') }}</h2>
                    <p>{{ __('marketing.solutions.organization.example_3_body') }}</p>
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
                        <span>{{ __('marketing.solutions.organization.outcome_label') }}</span>
                    </div>

                    <div class="section-title">

                        <h2>{{ __('marketing.solutions.organization.outcome_title') }}</h2>
                    </div>

                    <ul class="site-list">
                      <li>{{ __('marketing.solutions.organization.outcome_item_1') }}</li>
                      <li>{{ __('marketing.solutions.organization.outcome_item_2') }}</li>
                      <li>{{ __('marketing.solutions.organization.outcome_item_3') }}</li>
                      <li>{{ __('marketing.solutions.organization.outcome_item_4') }}</li>
                      <li>{{ __('marketing.solutions.organization.outcome_item_5') }}</li>
                    </ul>
                    <div class="action-btn" style="margin-top: 10px;">
                        <a href="{{ route('marketing.demo') }}" class="site-btn-dark">{{ __('frontend.cta.get_org_consultation') }}</a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
@endsection
