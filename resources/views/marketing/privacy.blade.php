@extends('marketing.layout')

@section('title', __('marketing.titles.privacy'))

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>{{ __('frontend.footer.privacy') }}</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">{{ __('frontend.nav.home') }} </a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.privacy') }}"> {{ __('frontend.footer.privacy') }} </a></span>
            </div>
        </div>
    </div>
</section>

<section class="policy-section">
    <div class="site-container">

        <div class="policy-content">
            <div class="inner-text-block">
                <h2>{{ __('marketing.privacy.intro_title') }}</h2>
                <p>{{ __('marketing.privacy.intro_body') }}</p>
            </div>
            <div class="inner-text-block">

                <h3>{{ __('marketing.privacy.section_1_title') }}</h3>
                <ul>
                    <li>{{ __('marketing.privacy.section_1_item_1') }}</li>
                    <li>{{ __('marketing.privacy.section_1_item_2') }}</li>
                    <li>{{ __('marketing.privacy.section_1_item_3') }}</li>
                    <li>{{ __('marketing.privacy.section_1_item_4') }}</li>
                    <li>{{ __('marketing.privacy.section_1_item_5') }}</li>
                </ul>
            </div>

            <div class="inner-text-block">

                <h3>{{ __('marketing.privacy.section_2_title') }}</h3>
                <ul>
                    <li>{{ __('marketing.privacy.section_2_item_1') }}</li>
                    <li>{{ __('marketing.privacy.section_2_item_2') }}</li>
                    <li>{{ __('marketing.privacy.section_2_item_3') }}</li>
                </ul>
            </div>

            <div class="inner-text-block">


                <h3>{{ __('marketing.privacy.section_3_title') }}</h3>
                <ul>
                    <li>{{ __('marketing.privacy.section_3_item_1') }}</li>
                    <li>{{ __('marketing.privacy.section_3_item_2') }}</li>
                    <li>{{ __('marketing.privacy.section_3_item_3') }}</li>
                    <li>{{ __('marketing.privacy.section_3_item_4') }}</li>
                </ul>
            </div>


            <div class="inner-text-block">


                <h3>{{ __('marketing.privacy.section_4_title') }}</h3>
                <ul>
                    <li>{{ __('marketing.privacy.section_4_item_1') }}</li>
                    <li>{{ __('marketing.privacy.section_4_item_2') }}</li>
                    <li>{{ __('marketing.privacy.section_4_item_3') }}</li>
                    <li>{{ __('marketing.privacy.section_4_item_4') }}</li>
                    <li>{{ __('marketing.privacy.section_4_item_5') }}</li>
                </ul>
            </div>
        </div>

        <div class="policy-sidebar">
            <a href="{{ route('marketing.privacy') }}" class="policy-btn active">{{ __('frontend.footer.privacy') }}</a>
            <a href="{{ route('marketing.terms') }}" class="policy-btn">{{ __('frontend.footer.terms') }}</a>
            <a href="{{ route('marketing.data_security') }}" class="policy-btn">{{ __('marketing.data_security.page_heading') }}</a>
        </div>

    </div>
</section>
@endsection
