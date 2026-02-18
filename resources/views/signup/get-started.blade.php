@extends('marketing.layout')

@section('title', 'Asylon | Get Started')

@section('content')
<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>{{ __('Get Started with Asylon') }}</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">Home</a></span>
                <span>/</span>
                <span><a href="{{ route('signup.show') }}">{{ __('Get Started') }}</a></span>
            </div>
        </div>
    </div>
</section>

<section class="block-left py-5 bg-light">
    <div class="site-container container">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h4 mb-2">{{ __('Create your organization and admin account to begin.') }}</h1>
                        <p class="text-muted mb-4">{{ __('We will set up your portal and send a confirmation email to the admin contact below.') }}</p>

                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('signup.store') }}" class="row g-3">
                            @csrf
                            <div class="col-12">
                                <label for="org_name" class="form-label">{{ __('Organization Name') }}</label>
                                <input type="text" id="org_name" name="org_name" value="{{ old('org_name') }}" required autofocus class="form-control">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="org_type" class="form-label">{{ __('Organization Type') }}</label>
                                <select id="org_type" name="org_type" class="form-select" required>
                                    @foreach (['school' => __('School'), 'church' => __('Church'), 'organization' => __('Organization'), 'other' => __('Other')] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('org_type') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="plan_slug" class="form-label">{{ __('Plan (optional)') }}</label>
                                <select id="plan_slug" name="plan_slug" class="form-select">
                                    <option value="">{{ __('Starter (default)') }}</option>
                                    @foreach ($plans as $plan)
                                        <option value="{{ $plan->slug }}" @selected(old('plan_slug') == $plan->slug)>
                                            {{ $plan->name }}
                                            @if ($plan->trial_days > 0)
                                                ({{ $plan->trial_days }} {{ __('day trial') }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label">{{ __('Admin Full Name') }}</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="form-control">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label">{{ __('Admin Work Email') }}</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="form-control">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="password" class="form-label">{{ __('Password') }}</label>
                                <input type="password" id="password" name="password" required autocomplete="new-password" class="form-control">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" required class="form-control">
                            </div>

                            <div class="col-12 d-flex justify-content-end pt-2">
                                <button type="submit" class="site-btn-dark px-4 py-2">
                                    {{ __('Create Account') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
