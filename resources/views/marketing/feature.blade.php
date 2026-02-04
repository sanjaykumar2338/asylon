@extends('marketing.layout')

@section('title', __('marketing.titles.feature'))

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>{{ __('marketing.feature.page_heading') }}</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">{{ __('frontend.nav.home') }} </a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.feature') }}">{{ __('marketing.feature.page_heading') }}</a></span>

            </div>

        </div>
    </div>
</section>

<section class="Feature-Block block-left2">
    <div class="site-container">

        <div class="section-head text-center">

            <div class="section-subtitle">
                <span>{{ __('marketing.feature.platform_label') }}</span>
            </div>
            <div class="section-title">
                <h2>{{ __('marketing.feature.platform_title') }}</h2>
            </div>
            <p>{{ __('marketing.feature.platform_body') }}</p>
        </div>
        <div class="Feature-Grid">


            <div class="Feature-Card">
                <div class="card-head">
                    <div class="icon">
                        <img src="{{ $assetBase }}/images/!Vector.png" alt="">
                    </div>
                    <div class="card-title">
                        <h2>{{ __('marketing.feature.reporting_portal.title') }}</h2>
                    </div>
                </div>
                <div class="card-text">
                    <ul>
                        <li>{{ __('marketing.feature.reporting_portal.item_1') }}</li>
                        <li>{{ __('marketing.feature.reporting_portal.item_2') }}</li>
                        <li>{{ __('marketing.feature.reporting_portal.item_3') }}</li>
                        <li>{{ __('marketing.feature.reporting_portal.item_4') }}</li>
                    </ul>
                </div>
            </div>

            <div class="Feature-Card">
                <div class="card-head">
                    <div class="icon">
                        <img src="{{ $assetBase }}/images/uVector.png" alt="">
                    </div>
                    <div class="card-title">
                        <h2>{{ __('marketing.feature.role_access.title') }}</h2>
                    </div>
                </div>
                <div class="card-text">
                    <ul>
                        <li>{{ __('marketing.feature.role_access.item_1') }}</li>
                        <li>{!! __('marketing.feature.role_access.item_2') !!}</li>
                        <li>{{ __('marketing.feature.role_access.item_3') }}</li>
                    </ul>
                </div>
            </div>

            <div class="Feature-Card">
                <div class="card-head">
                    <div class="icon">
                        <img src="{{ $assetBase }}/images/sVector.png" alt="">
                    </div>
                    <div class="card-title">
                        <h2>{{ __('marketing.feature.case_management.title') }}</h2>
                    </div>
                </div>
                <div class="card-text">
                    <ul>
                        <li>{!! __('marketing.feature.case_management.item_1') !!}</li>
                        <li>{!! __('marketing.feature.case_management.item_2') !!}</li>
                        <li>{!! __('marketing.feature.case_management.item_3') !!}</li>
                    </ul>
                </div>
            </div>

            <div class="Feature-Card">
                <div class="card-head">
                    <div class="icon">
                        <img src="{{ $assetBase }}/images/b1Vector.png" alt="">
                    </div>
                    <div class="card-title">
                        <h2>{{ __('marketing.feature.notifications.title') }}</h2>
                    </div>
                </div>
                <div class="card-text">
                    <ul>
                        <li>{!! __('marketing.feature.notifications.item_1') !!}</li>
                        <li>{!! __('marketing.feature.notifications.item_2') !!}</li>
                        <li>{{ __('marketing.feature.notifications.item_3') }}</li>
                    </ul>
                </div>
            </div>

            <div class="Feature-Card">
                <div class="card-head">
                    <div class="icon">
                        <img src="{{ $assetBase }}/images/g1Vector.png" alt="">
                    </div>
                    <div class="card-title">
                        <h2>{{ __('marketing.feature.analytics.title') }}</h2>
                    </div>
                </div>
                <div class="card-text">
                    <ul>
                        <li>{{ __('marketing.feature.analytics.item_1') }}</li>
                        <li>{!! __('marketing.feature.analytics.item_2') !!}</li>
                    </ul>
                </div>
            </div>

            <div class="Feature-Card">
                <div class="card-head">
                    <div class="icon">
                        <img src="{{ $assetBase }}/images/se.png" alt="">
                    </div>
                    <div class="card-title">
                        <h2>{{ __('marketing.feature.security.title') }}</h2>
                    </div>
                </div>
                <div class="card-text">
                    <ul>
                        <li>{{ __('marketing.feature.security.item_1') }}</li>
                        <li>{{ __('marketing.feature.security.item_2') }}</li>
                        <li>{{ __('marketing.feature.security.item_3') }}</li>
                        <li>{!! __('marketing.feature.security.item_4') !!}</li>
                    </ul>
                </div>
            </div>

        </div>
        <div class="root-btn text-center" >
            <a href="{{ route('marketing.demo') }}" class="site-btn-dark">{{ __('frontend.cta.schedule_consultation') }}</a>
        </div>
    </div>
</section>
@endsection
