@extends('marketing.layout')

@section('title', 'Asylon | Demo Form')

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>Demo Form</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">Home </a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.demo') }}">Demo Form </a></span>
            </div>
        </div>
    </div>
</section>

<section class="demo-form-section block-left">
    <div class="site-container">
        <div class="section-title text-center">
            <h2>Book a Demo Form</h2>
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
                    <label>First Name</label>
                    <input type="text" placeholder="Enter your first name" name="first_name" value="{{ old('first_name') }}" required>
                </div>

                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" placeholder="Last Name" name="last_name" value="{{ old('last_name') }}" required>
                </div>

                <div class="form-group">
                    <label>Organization Name</label>
                    <input type="text" placeholder="Enter organization name" name="organization" value="{{ old('organization') }}" required>
                </div>

                <div class="form-group">
                    <label>Type</label>
                    <select name="organization_type" required>
                        <option value="" disabled {{ old('organization_type') ? '' : 'selected' }}>Select Organization Type</option>
                        <option value="School" @selected(old('organization_type') === 'School')>School</option>
                        <option value="Church" @selected(old('organization_type') === 'Church')>Church</option>
                        <option value="Workplace" @selected(old('organization_type') === 'Workplace')>Workplace</option>
                        <option value="Other" @selected(old('organization_type') === 'Other')>Other</option>

                    </select>
                </div>

                <div class="form-group">
                    <label>Role</label>
                    <input type="text" placeholder="Enter role" name="role" value="{{ old('role') }}">
                </div>

                <div class="form-group">
                    <label>Work Email</label>
                    <input type="email" placeholder="Enter your email" name="email" value="{{ old('email') }}" required>
                    <small>Please use your work or organization email.</small>
                </div>

                <div class="form-group">
                    <label>Phone Number <span>(optional)</span></label>
                    <input type="tel" placeholder="Enter Phone Number" name="phone" value="{{ old('phone') }}">
                </div>

                <div class="form-group g1">
                    <label>Preferred Meeting Type</label>
                    <div class="radio-group custom-radio">
                        <label class="radio-box">
                            <input type="radio" name="meeting" value="15-minute intro" @checked(old('meeting') === '15-minute intro')>
                            <span class="radio-custom"></span>
                            <span class="radio-text">15-minute intro</span>
                        </label>

                        <label class="radio-box">
                            <input type="radio" name="meeting" value="30-minute full demo" @checked(old('meeting') === '30-minute full demo')>
                            <span class="radio-custom"></span>
                            <span class="radio-text">30-minute full demo</span>
                        </label>
                    </div>
                </div>

                <div class="form-group full">
                    <label>Preferred Time Window (CST)</label>
                    <select name="time_window">
                        <option value="" disabled {{ old('time_window') ? '' : 'selected' }}>Select</option>
                        <option value="Morning" @selected(old('time_window') === 'Morning')>Morning</option>
                        <option value="Afternoon" @selected(old('time_window') === 'Afternoon')>Afternoon</option>
                        <option value="Evening" @selected(old('time_window') === 'Evening')>Evening</option>

                    </select>
                </div>

                <div class="form-group full">
                    <label>What are your top 1-2 concerns right now?</label>
                    <textarea placeholder="Type here" name="concerns">{{ old('concerns') }}</textarea>
                </div>
            </div>

            <button type="submit" class="site-btn-dark">Book Demo</button>
        </form>
    </div>
</section>
@endsection
