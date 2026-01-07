@extends('marketing.layout')

@section('title', 'Asylon | Forgot Password')

@push('meta')
    <style>
        .auth-form-section .section-title p {
            margin-top: 8px;
            color: #484848;
        }

        .auth-form-footer {
            margin-top: 18px;
            text-align: center;
            color: #484848;
            font-size: 14px;
        }

        .auth-form-footer a {
            color: #0b1f3b;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <section class="inner-pages-header">
        <div class="site-container">
            <div class="page-header">
                <div class="section-title">
                    <h2>{{ __('Forgot Password') }}</h2>
                </div>
                <div class="page-link">
                    <span><a href="{{ route('marketing.home') }}">{{ __('Home') }} </a></span>
                    <span>/</span>
                    <span><a href="{{ route('password.request') }}">{{ __('Forgot Password') }}</a></span>
                </div>
            </div>
        </div>
    </section>

    <section class="demo-form-section block-left auth-form-section">
        <div class="site-container">
            <div class="section-title text-center">
                <h2>{{ __('Reset your password') }}</h2>
                <p>{{ __('Enter your work email and we will email you a reset link to choose a new password.') }}</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success text-center" role="status">
                    {{ session('status') }}
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

            <form class="demo-form" method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-grid">
                    <div class="form-group full">
                        <label for="email">{{ __('Work Email') }}</label>
                        <input id="email"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="{{ __('Enter your email') }}"
                               required
                               autofocus>
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="site-btn-dark">
                    {{ __('Email Password Reset Link') }}
                </button>

                <div class="auth-form-footer">
                    {{ __('Remembered your password?') }}
                    <a href="{{ route('login') }}">{{ __('Log in') }}</a>
                </div>
            </form>
        </div>
    </section>
@endsection
