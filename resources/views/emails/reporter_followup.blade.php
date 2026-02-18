@extends('emails.layouts.brand')

@section('content')
    <span class="pill">{{ __('notifications.reporter_followup.badge') }}</span>
    <h1>{{ $templateSubject ?? __('notifications.reporter_followup.new_message_subject', ['id' => $report->id]) }}</h1>

    @if(!empty($templateBody))
        @foreach(preg_split('/\\n\\n+/', $templateBody) as $block)
            <p>{{ trim($block) }}</p>
        @endforeach
    @else
        <p class="meta">{{ __('notifications.labels.organization') }}: {{ $orgName }}</p>
        <p class="meta">{{ __('notifications.labels.category') }}: {{ $categoryLabel }}</p>
        <p class="meta">{{ __('notifications.labels.submitted') }}: {{ $submitted }}</p>

        <div class="panel">
            <p style="margin: 0 0 8px;">{{ __('notifications.reporter_followup.message_intro') }}</p>
            <p style="margin: 0;">&ldquo;{{ $messageBody }}&rdquo;</p>
        </div>

        <p>{{ __('notifications.reporter_followup.dashboard_reminder') }}</p>

        <p class="meta" style="margin-top: 12px;">
            {{ __('notifications.labels.followup_portal') }} <a href="{{ $followupUrl }}">{{ $followupUrl }}</a>
        </p>
    @endif

    <a href="{{ $dashboardUrl }}" class="btn">{{ __('notifications.actions.open_report') }}</a>
    <p class="meta" style="margin-top: 12px;">
        {{ __('notifications.labels.followup_portal') }} <a href="{{ $followupUrl }}">{{ $followupUrl }}</a>
    </p>
@endsection
