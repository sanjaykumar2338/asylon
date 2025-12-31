@extends('marketing.layout')

@section('title', 'Asylon | Contact')

@section('content')
@php($assetBase = asset('asylonhtml/asylon'))
<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>Contact Us</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">Home </a></span>
                <span>/</span>
                <span><a href="{{ route('marketing.contact') }}">Contact Us </a></span>
            </div>
        </div>
    </div>
</section>

<section class="contact-asylon block-left ">
    <div class="site-container">
        <div class="contact-grid">
            <div class="contact-bx">
                <div class="section-title">
                    <h2>Contact Asylon</h2>
                    <p>Have questions about the platform or not ready to book a demo <br> yet? Send us a message and
                        we'll respond within 1 business day.</p>
                </div>


                <ul>
                    <li><a href="tel:0425600335"><img src="{{ $assetBase }}/images/callVector.png" alt="">0425600335</a></li>

                    <li><a href="mailto:info@emergeacademy.com"><img src="{{ $assetBase }}/images/emailVector.png"
                                alt="">info@emergeacademy.com</a></li>
                    <li><a href="mailto:info@emergeacademy.com"><img src="{{ $assetBase }}/images/locationVector.png" alt="">125 / 18
                            - 20 Edinburgh St, Oakleigh <br> South, Victoria, 3167, Australia</a></li>


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
                        <label for="">First Name</label>
                        <input type="text" placeholder="Enter your first name" name="first_name" value="{{ old('first_name') }}" required>
                    </div>

                    <div class="input-root">
                        <label for="">Last Name</label>
                        <input type="text" placeholder="Last Name" name="last_name" value="{{ old('last_name') }}" required>
                    </div>

                    <div class="input-root full-width">
                        <label for="">Message</label>
                        <textarea name="message" placeholder="Type here" id="" required>{{ old('message') }}</textarea>
                    </div>
                    <div class="input-root full-width">
                        <button type="submit" class="site-btn-dark">Send</button>
                    </div>
                </form>
                <div class="note">
                    <p>Note: We usually respond within 1 business day.</p>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
