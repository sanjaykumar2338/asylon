<x-admin-layout>
    <x-slot name="header">
        {{ __('Reviewer Dashboard') }}
    </x-slot>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <style>
            .hero-panel {
                background: linear-gradient(135deg, #0b1f3b, #173f74);
                color: #fff;
                border-radius: 18px;
            }
            .stat-card {
                border: 0;
                border-radius: 14px;
                color: #fff;
                min-height: 170px;
            }
            .stat-icon {
                height: 46px;
                width: 46px;
                border-radius: 12px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: rgba(255,255,255,0.18);
            }
            .card-lite {
                border-radius: 14px;
                border: 1px solid #e5e7eb;
            }
        </style>
    @endpush

    @php
        $user = auth()->user();
        $statBlocks = [
            [
                'label' => __('Total reports'),
                'value' => number_format($stats['totalReports'] ?? 0),
                'icon' => 'fa-clipboard-list',
                'bg' => 'bg-primary',
                'link' => route('reviews.index'),
                'linkText' => __('View reports'),
            ],
            [
                'label' => __('Open reports'),
                'value' => number_format($stats['openReports'] ?? 0),
                'icon' => 'fa-folder-open',
                'bg' => 'bg-success',
                'link' => route('reviews.index', ['status' => 'open']),
                'linkText' => __('Jump to open'),
            ],
            [
                'label' => __('Urgent reports'),
                'value' => number_format($stats['urgentReports'] ?? 0),
                'icon' => 'fa-triangle-exclamation',
                'bg' => 'bg-danger',
                'link' => route('reviews.index', ['urgent' => '1']),
                'linkText' => __('View urgent queue'),
            ],
            [
                'label' => __('Users'),
                'value' => number_format($stats['totalUsers'] ?? 0),
                'icon' => 'fa-users',
                'bg' => 'bg-info',
                'link' => $user?->hasRole('platform_admin') ? route('admin.users.index') : null,
                'linkText' => $user?->hasRole('platform_admin') ? __('Manage users') : __('Active in your org'),
            ],
        ];
    @endphp

    <div class="hero-panel p-4 p-md-5 mb-4 shadow-sm">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <div class="text-uppercase small opacity-75 mb-1">{{ __('Welcome back') }}</div>
                <h2 class="fw-semibold mb-2">{{ $user?->name ?? __('User') }}</h2>
                <p class="mb-0 opacity-75">{{ __('Stay on top of reports, urgency, and team activity from one place.') }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('reviews.index') }}" class="btn btn-light text-primary fw-semibold">
                    <i class="fa-solid fa-list-check me-2"></i>{{ __('Review reports') }}
                </a>
                <a href="{{ route('reviews.index', ['urgent' => '1']) }}" class="btn btn-outline-light">
                    <i class="fa-solid fa-bolt me-2"></i>{{ __('Urgent queue') }}
                </a>
                <a href="{{ route('admin.analytics') }}" class="btn btn-outline-light">
                    <i class="fa-solid fa-chart-line me-2"></i>{{ __('View analytics') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach ($statBlocks as $stat)
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card shadow-sm {{ $stat['bg'] }}">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="stat-icon">
                                <i class="fa-solid {{ $stat['icon'] }}"></i>
                            </span>
                            <span class="fw-semibold">{{ $stat['label'] }}</span>
                        </div>
                        <div>
                            <div class="display-6 fw-bold mb-1">{{ $stat['value'] }}</div>
                            @if ($stat['link'])
                                <a href="{{ $stat['link'] }}" class="text-white text-decoration-none fw-semibold">
                                    {{ $stat['linkText'] }} <i class="fa-solid fa-arrow-right-long ms-1"></i>
                                </a>
                            @else
                                <span class="opacity-75">{{ $stat['linkText'] }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card card-lite shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">{{ __('Quick actions') }}</h5>
                        <span class="badge bg-primary-subtle text-primary">{{ __('Today') }}</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('reviews.index') }}" class="btn btn-outline-primary">
                            <i class="fa-solid fa-clipboard-list me-2"></i>{{ __('Review queue') }}
                        </a>
                        <a href="{{ route('reviews.index', ['status' => 'open']) }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-folder-open me-2"></i>{{ __('Open reports') }}
                        </a>
                        <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-bell me-2"></i>{{ __('common.notifications') }}
                        </a>
                        @if ($user?->hasRole('platform_admin'))
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-user-gear me-2"></i>{{ __('Manage users') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card card-lite shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">{{ __('Support & resources') }}</h5>
                        <i class="fa-solid fa-life-ring text-primary"></i>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex align-items-start gap-2 mb-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <div>
                                <strong>{{ __('Setup checklist') }}</strong>
                                <div class="text-secondary small">{{ __('Confirm access, notifications, and escalation rules.') }}</div>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2 mb-2">
                            <i class="fa-solid fa-graduation-cap text-info mt-1"></i>
                            <div>
                                <strong>{{ __('Training resources') }}</strong>
                                <div class="text-secondary small">
                                    <a href="{{ route('blog.index') }}" class="text-decoration-none">{{ __('Visit the blog') }}</a>
                                    {{ __(' for best practices and updates.') }}
                                </div>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-envelope text-primary mt-1"></i>
                            <div>
                                <strong>{{ __('Need help fast?') }}</strong>
                                <div class="text-secondary small">
                                    <a href="mailto:{{ config('asylon.support_email', 'support@asylon.cc') }}" class="text-decoration-none">
                                        {{ config('asylon.support_email', 'support@asylon.cc') }}
                                    </a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
