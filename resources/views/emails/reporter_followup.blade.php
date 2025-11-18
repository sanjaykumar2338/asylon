@extends('emails.layouts.brand')

@section('content')
    <span class="pill">{{ __('Reporter follow-up') }}</span>
    <h1>{{ __('New message on case #:id', ['id' => $report->id]) }}</h1>
    <p class="meta">{{ __('Organization') }}: {{ $orgName }}</p>
    <p class="meta">{{ __('Category') }}: {{ $categoryLabel }}</p>
    <p class="meta">{{ __('Submitted') }}: {{ $submitted }}</p>

    <div class="panel">
        <p style="margin: 0 0 8px;">{{ __('Message from reporter:') }}</p>
        <p style="margin: 0;">“{{ $messageBody }}”</p>
    </div>

    <p>{{ __('Please log any responses directly in the dashboard to keep the case history complete.') }}</p>

    <a href="{{ $dashboardUrl }}" class="btn">{{ __('Open report in dashboard') }}</a>
    <p class="meta" style="margin-top: 12px;">
        {{ __('Public follow-up portal:') }} <a href="{{ $followupUrl }}">{{ $followupUrl }}</a>
    </p>
@endsection
