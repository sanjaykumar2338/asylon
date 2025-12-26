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

        <form class="demo-form " action="#">
            <div class="form-grid">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" placeholder="Enter your first name" name="first_name">
                </div>

                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" placeholder="Last Name" name="last_name">
                </div>

                <div class="form-group">
                    <label>Organization Name</label>
                    <input type="text" placeholder="Enter organization name" name="organization">
                </div>

                <div class="form-group">
                    <label>Type</label>
                    <select name="organization_type">
                        <option>Select Organization Type</option>
                        <option>School</option>
                        <option>Church</option>
                        <option>Workplace</option>
                        <option>Other</option>

                    </select>
                </div>

                <div class="form-group">
                    <label>Role</label>
                    <input type="text" placeholder="Enter role" name="role">
                </div>

                <div class="form-group">
                    <label>Work Email</label>
                    <input type="email" placeholder="Enter your email" name="email">
                    <small>Please use your work or organization email.</small>
                </div>

                <div class="form-group">
                    <label>Phone Number <span>(optional)</span></label>
                    <input type="tel" placeholder="Enter Phone Number" name="phone">
                </div>

                <div class="form-group g1">
                    <label>Preferred Meeting Type</label>
                    <div class="radio-group custom-radio">
                        <label class="radio-box">
                            <input type="radio" name="meeting" value="15-minute intro">
                            <span class="radio-custom"></span>
                            <span class="radio-text">15-minute intro</span>
                        </label>

                        <label class="radio-box">
                            <input type="radio" name="meeting" value="30-minute full demo">
                            <span class="radio-custom"></span>
                            <span class="radio-text">30-minute full demo</span>
                        </label>
                    </div>
                </div>

                <div class="form-group full">
                    <label>Preferred Time Window (CST)</label>
                    <select name="time_window">
                        <option>Select</option>
                        <option>Morning</option>
                        <option>Afternoon</option>
                        <option>Evening</option>

                    </select>
                </div>

                <div class="form-group full">
                    <label>What are your top 1-2 concerns right now?</label>
                    <textarea placeholder="Type here" name="concerns"></textarea>
                </div>
            </div>

            <button type="submit" class="site-btn-dark">Book Demo</button>
        </form>
    </div>
</section>
@endsection
