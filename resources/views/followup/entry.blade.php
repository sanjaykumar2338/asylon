@extends('marketing.layout')

@section('title', 'Asylon | Follow Up')

@section('content')
@php($supportEmail = config('asylon.support_email', 'support@asylon.cc'))
@php($infoEmail = config('asylon.info_email', 'info@asylon.cc'))

<section class="inner-pages-header">
    <div class="site-container">
        <div class="page-header">
            <div class="section-title">
                <h2>{{ __('Follow up on an existing case') }}</h2>
            </div>
            <div class="page-link">
                <span><a href="{{ route('marketing.home') }}">Home</a></span>
                <span>/</span>
                <span><a href="{{ route('report.create') }}">Submit a Report</a></span>
                <span>/</span>
                <span><a href="{{ route('followup.entry') }}">Follow Up</a></span>
            </div>
        </div>
    </div>
</section>

<section class="block-left py-5 bg-light">
    <div class="site-container container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h4 mb-2">{{ __('Follow up on an existing case') }}</h1>
                        <p class="text-muted mb-4">
                            {{ __('Paste the Case ID or follow-up link you saved after submitting your report.') }}
                        </p>

                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('followup.redirect') }}" class="mt-3">
                            @csrf
                            <div class="mb-4">
                                <label for="case_id" class="form-label">{{ __('Case ID or follow-up link') }}</label>
                                <input type="text"
                                       name="case_id"
                                       id="case_id"
                                       value="{{ old('case_id') }}"
                                       required
                                       class="form-control"
                                       placeholder="{{ __('e.g. case ID or https://.../followup/your-token') }}">
                                @error('case_id')
                                    <p class="text-danger small mb-0 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <a href="{{ route('report.create') }}" class="text-primary">
                                    {{ __('Submit a new report instead') }}
                                </a>
                                <button type="submit" class="site-btn-dark px-4 py-2">
                                    {{ __('Continue') }}
                                </button>
                            </div>
                        </form>

                        <div class="mt-4 text-muted small">
                            <p class="mb-1">{{ __('Need help finding your case ID? Check the confirmation message you saved when you first submitted your report.') }}</p>
                            <p class="mb-0">
                                {{ __('Questions?') }}
                                <a href="mailto:{{ $infoEmail }}" class="text-primary">{{ $infoEmail }}</a>
                                {{ __('or') }}
                                <a href="mailto:{{ $supportEmail }}" class="text-primary">{{ $supportEmail }}</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
