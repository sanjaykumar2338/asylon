@extends('marketing.layout')

@section('title', __('marketing.titles.data_security'))

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>{{ __('marketing.data_security.page_heading') }}</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">{{ __('frontend.nav.home') }} </a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.data_security') }}"> {{ __('marketing.data_security.page_heading') }} </a></span>
            </div>
        </div>
    </div>
</section>

<section class="policy-section">
    <div class="site-container">

        <div class="policy-content">


            <div class="inner-text-block">
                <h2>{{ __('marketing.data_security.commitment_title') }}</h2>
                <p>{{ __('marketing.data_security.commitment_body') }}</p>
            </div>
            <div class="inner-text-block">

                <h3>{{ __('marketing.data_security.hosting_title') }}</h3>
                <ul>
                    <li>{{ __('marketing.data_security.hosting_item_1') }}</li>
                    <li>{{ __('marketing.data_security.hosting_item_2') }}</li>
                    <li>{{ __('marketing.data_security.hosting_item_3') }}</li>
                </ul>
            </div>

            <div class="inner-text-block">

                <h3>{{ __('marketing.data_security.org_responsibilities_title') }}</h3>
                <ul>
                    <li>{{ __('marketing.data_security.org_responsibilities_item_1') }}</li>
                    <li>{{ __('marketing.data_security.org_responsibilities_item_2') }}</li>
                    <li>{{ __('marketing.data_security.org_responsibilities_item_3') }}</li>
                    <li>{{ __('marketing.data_security.org_responsibilities_item_4') }}</li>
                </ul>
            </div>

            <div class="inner-text-block">


                <h3>{{ __('marketing.data_security.data_ownership_title') }}</h3>
                <p>{{ __('marketing.data_security.data_ownership_body') }}</p>
            </div>



        </div>

        <div class="policy-sidebar">
            <a href="{{ route('marketing.privacy') }}" class="policy-btn ">{{ __('frontend.footer.privacy') }}</a>
            <a href="{{ route('marketing.terms') }}" class="policy-btn ">{{ __('frontend.footer.terms') }}</a>
            <a href="{{ route('marketing.data_security') }}" class="policy-btn active">{{ __('marketing.data_security.page_heading') }}</a>
        </div>

    </div>
</section>
@endsection
