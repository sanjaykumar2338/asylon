@extends('marketing.layout')

@section('title', __('marketing.titles.terms'))

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>{{ __('marketing.terms.page_heading') }}</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">{{ __('frontend.nav.home') }} </a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.terms') }}"> {{ __('marketing.terms.page_heading') }} </a></span>
            </div>
        </div>
    </div>
</section>

<section class="policy-section">
    <div class="site-container">

        <div class="policy-content">
            <div class="inner-text-block">
                <h2>{{ __('marketing.terms.section_1_title') }}</h2>
                <p>{{ __('marketing.terms.section_1_body') }}</p>
            </div>
            <div class="inner-text-block">

                <h3>{{ __('marketing.terms.section_2_title') }}</h3>
                <p>{{ __('marketing.terms.section_2_body') }}</p>
            </div>

            <div class="inner-text-block">

                <h3>{{ __('marketing.terms.section_3_title') }}</h3>
                <ul>
                    <li>{{ __('marketing.terms.section_3_item_1') }}</li>
                    <li>{{ __('marketing.terms.section_3_item_2') }}</li>
                    <li>{{ __('marketing.terms.section_3_item_3') }}</li>
                    <li>{{ __('marketing.terms.section_3_item_4') }}</li>
                </ul>
            </div>

            <div class="inner-text-block">


                <h3>{{ __('marketing.terms.section_4_title') }}</h3>
                <ul>
                    <li>{{ __('marketing.terms.section_4_item_1') }}</li>
                    <li>{{ __('marketing.terms.section_4_item_2') }}</li>
                    <li>{{ __('marketing.terms.section_4_item_3') }}</li>
                    <li>{{ __('marketing.terms.section_4_item_4') }}</li>
                </ul>
            </div>


            <div class="inner-text-block">


                <h3>{{ __('marketing.terms.section_5_title') }}</h3>
                <p>{{ __('marketing.terms.section_5_body') }}</p>
            </div>
        </div>

        <div class="policy-sidebar">
            <a href="{{ route('marketing.privacy') }}" class="policy-btn ">{{ __('frontend.footer.privacy') }}</a>
            <a href="{{ route('marketing.terms') }}" class="policy-btn active">{{ __('frontend.footer.terms') }}</a>
            <a href="{{ route('marketing.data_security') }}" class="policy-btn">{{ __('marketing.data_security.page_heading') }}</a>
        </div>

    </div>
</section>
@endsection
