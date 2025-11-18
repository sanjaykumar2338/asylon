@extends('emails.layouts.brand')

@section('content')
    <span class="pill">{{ __('Urgent alert') }}</span>
    <h1>{{ __('Urgent case: :category', ['category' => $categoryLabel]) }}</h1>
    <p class="meta">{{ __('Organization') }}: {{ $orgName }}</p>
    <p class="meta">{{ __('Case ID') }}: {{ $report->id }}</p>
    <p class="meta">{{ __('Submitted') }}: {{ $report->created_at?->timezone(config('app.timezone'))->format('M d, Y H:i') }}</p>
    <p class="meta">{{ __('Violation date') }}: {{ $report->violation_date?->format('M d, Y') ?? __('Not provided') }}</p>

    <div class="panel">
        <p style="margin: 0;">{{ __('Please review and respond as soon as possible.') }}</p>
    </div>

    <a href="{{ $reportUrl }}" class="btn">{{ __('Open in dashboard') }}</a>
@endsection
