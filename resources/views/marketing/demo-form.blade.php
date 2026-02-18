@extends('marketing.layout')

@section('title', __('marketing.titles.demo'))

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>{{ __('marketing.demo.page_heading') }}</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">{{ __('frontend.nav.home') }} </a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.demo') }}">{{ __('marketing.demo.page_heading') }} </a></span>
            </div>
        </div>
    </div>
</section>

<section class="demo-form-section block-left">
    <div class="site-container">
        <div class="section-title text-center">
            <h2>{{ __('marketing.demo.section_title') }}</h2>
        </div>

        @if (session('success'))
            <div class="alert alert-success text-center" role="status">
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

        <form class="demo-form" action="{{ route('marketing.demo.submit') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label for="demo-first-name">{{ __('marketing.demo.form.first_name') }}</label>
                    <input id="demo-first-name" type="text" placeholder="{{ __('marketing.demo.form.first_name_placeholder') }}" name="first_name" value="{{ old('first_name') }}" required>
                </div>

                <div class="form-group">
                    <label for="demo-last-name">{{ __('marketing.demo.form.last_name') }}</label>
                    <input id="demo-last-name" type="text" placeholder="{{ __('marketing.demo.form.last_name_placeholder') }}" name="last_name" value="{{ old('last_name') }}" required>
                </div>

                <div class="form-group">
                    <label for="demo-organization">{{ __('marketing.demo.form.organization') }}</label>
                    <input id="demo-organization" type="text" placeholder="{{ __('marketing.demo.form.organization_placeholder') }}" name="organization" value="{{ old('organization') }}" required>
                </div>

                <div class="form-group">
                    <label for="demo-organization-type">{{ __('marketing.demo.form.organization_type') }}</label>
                    <select id="demo-organization-type" name="organization_type" required>
                        <option value="" disabled {{ old('organization_type') ? '' : 'selected' }}>{{ __('marketing.demo.form.organization_type_placeholder') }}</option>
                        <option value="School" @selected(old('organization_type') === 'School')>{{ __('marketing.demo.form.organization_type_school') }}</option>
                        <option value="Church" @selected(old('organization_type') === 'Church')>{{ __('marketing.demo.form.organization_type_church') }}</option>
                        <option value="Workplace" @selected(old('organization_type') === 'Workplace')>{{ __('marketing.demo.form.organization_type_workplace') }}</option>
                        <option value="Other" @selected(old('organization_type') === 'Other')>{{ __('marketing.demo.form.organization_type_other') }}</option>

                    </select>
                </div>

                <div class="form-group">
                    <label for="demo-role">{{ __('marketing.demo.form.role') }}</label>
                    <input id="demo-role" type="text" placeholder="{{ __('marketing.demo.form.role_placeholder') }}" name="role" value="{{ old('role') }}">
                </div>

                <div class="form-group">
                    <label for="demo-email">{{ __('marketing.demo.form.email') }}</label>
                    <input id="demo-email" type="email" placeholder="{{ __('marketing.demo.form.email_placeholder') }}" name="email" value="{{ old('email') }}" required>
                    <small>{{ __('marketing.demo.form.email_hint') }}</small>
                </div>

                <div class="form-group">
                    <label for="demo-phone">{{ __('marketing.demo.form.phone') }} <span>{{ __('marketing.demo.form.optional') }}</span></label>
                    <input id="demo-phone" type="tel" placeholder="{{ __('marketing.demo.form.phone_placeholder') }}" name="phone" value="{{ old('phone') }}">
                </div>

                <div class="form-group g1">
                    <label>{{ __('marketing.demo.form.meeting_type') }}</label>
                    <div class="radio-group custom-radio">
                        <label class="radio-box">
                            <input type="radio" name="meeting" value="15-minute intro" @checked(old('meeting') === '15-minute intro')>
                            <span class="radio-custom"></span>
                            <span class="radio-text">{{ __('marketing.demo.form.meeting_type_intro') }}</span>
                        </label>

                        <label class="radio-box">
                            <input type="radio" name="meeting" value="30-minute full demo" @checked(old('meeting') === '30-minute full demo')>
                            <span class="radio-custom"></span>
                            <span class="radio-text">{{ __('marketing.demo.form.meeting_type_full') }}</span>
                        </label>
                    </div>
                </div>

                <div class="form-group full">
                    <label for="demo-time-window">{{ __('marketing.demo.form.time_window') }}</label>
                    <select id="demo-time-window" name="time_window">
                        <option value="" disabled {{ old('time_window') ? '' : 'selected' }}>{{ __('marketing.demo.form.time_window_placeholder') }}</option>
                        <option value="Morning" @selected(old('time_window') === 'Morning')>{{ __('marketing.demo.form.time_window_morning') }}</option>
                        <option value="Afternoon" @selected(old('time_window') === 'Afternoon')>{{ __('marketing.demo.form.time_window_afternoon') }}</option>
                        <option value="Evening" @selected(old('time_window') === 'Evening')>{{ __('marketing.demo.form.time_window_evening') }}</option>

                    </select>
                </div>

                <div class="form-group full">
                    <label for="demo-concerns">{{ __('marketing.demo.form.concerns') }}</label>
                    <textarea id="demo-concerns" placeholder="{{ __('marketing.demo.form.concerns_placeholder') }}" name="concerns">{{ old('concerns') }}</textarea>
                </div>
            </div>

            <button type="submit" class="site-btn-dark">{{ __('frontend.cta.book_demo') }}</button>
        </form>
    </div>
</section>
@endsection
