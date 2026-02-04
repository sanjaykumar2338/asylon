@extends('marketing.layout')

@section('title', __('marketing.titles.contact'))

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>{{ __('marketing.contact.page_heading') }}</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">{{ __('frontend.nav.home') }} </a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.contact') }}">{{ __('marketing.contact.page_heading') }} </a></span>
            </div>
        </div>
    </div>
</section>

<section class="contact-asylon block-left ">
    <div class="site-container">
        <div class="contact-grid">
            <div class="contact-bx">
                <div class="section-title">
                    <h2>{{ __('marketing.contact.section_title') }}</h2>
                    <p>{!! __('marketing.contact.section_body') !!}</p>
                </div>


                <ul>
                    <li><a href="tel:0425600335"><img src="{{ $assetBase }}/images/callVector.png" alt="">0425600335</a></li>

                    <li><a href="mailto:info@asylon.app"><img src="{{ $assetBase }}/images/emailVector.png"
                                alt="">info@asylon.app</a></li>
                    <li><a href="https://www.google.com/maps/search/?api=1&query=6340+Lake+Worth+Blvd+%231048%2C+Fort+Worth%2C+TX+76135" target="_blank" rel="noreferrer"><img src="{{ $assetBase }}/images/locationVector.png" alt="">6340 Lake Worth Blvd #1048, Fort Worth, TX 76135</a></li>


                </ul>

            </div>

            <div class="contact-bx contact-right">
                @if (session('success'))
                    <div class="alert alert-success" role="status">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('marketing.contact.submit') }}" method="POST">
                    @csrf
                    <div class="input-root">
                        <label for="contact-first-name">{{ __('marketing.contact.form.first_name') }}</label>
                        <input id="contact-first-name" type="text" placeholder="{{ __('marketing.contact.form.first_name_placeholder') }}" name="first_name" value="{{ old('first_name') }}" required>
                    </div>

                    <div class="input-root">
                        <label for="contact-last-name">{{ __('marketing.contact.form.last_name') }}</label>
                        <input id="contact-last-name" type="text" placeholder="{{ __('marketing.contact.form.last_name_placeholder') }}" name="last_name" value="{{ old('last_name') }}" required>
                    </div>

                    <div class="input-root">
                        <label for="contact-email">{{ __('marketing.contact.form.email') }}</label>
                        <input id="contact-email" type="email" placeholder="{{ __('marketing.contact.form.email_placeholder') }}" name="email" value="{{ old('email') }}" required>
                    </div>

                    <div class="input-root full-width">
                        <label for="contact-message">{{ __('marketing.contact.form.message') }}</label>
                        <textarea id="contact-message" name="message" placeholder="{{ __('marketing.contact.form.message_placeholder') }}" required>{{ old('message') }}</textarea>
                    </div>
                    <div class="input-root full-width">
                        <button type="submit" class="site-btn-dark">{{ __('marketing.contact.form.submit') }}</button>
                    </div>
                </form>
                <div class="note">
                    <p>{{ __('marketing.contact.note') }}</p>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
