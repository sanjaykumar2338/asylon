@extends('emails.layouts.brand')

@section('content')
    <span class="pill">{{ __('notifications.urgent_alert.badge') }}</span>
    <h1>{{ __('notifications.urgent_alert.heading', ['category' => $categoryLabel]) }}</h1>
    <p class="meta">{{ __('notifications.labels.organization') }}: {{ $orgName }}</p>
    <p class="meta">{{ __('notifications.labels.case_id') }}: {{ $report->id }}</p>
    <p class="meta">{{ __('notifications.labels.submitted') }}: {{ $report->created_at?->timezone(config('app.timezone'))->format('M d, Y H:i') }}</p>
    <p class="meta">{{ __('notifications.labels.violation_date') }}: {{ $report->violation_date?->format('M d, Y') ?? __('notifications.labels.not_provided') }}</p>

    <div class="panel">
        <p style="margin: 0;">{{ __('notifications.urgent_alert.prompt') }}</p>
    </div>

    <a href="{{ $reportUrl }}" class="btn">{{ __('notifications.actions.open_dashboard') }}</a>
@endsection
