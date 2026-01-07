<x-admin-layout>
    <x-slot name="header">
        {{ __('dashboard.title') }}
    </x-slot>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <style>
            .dash-hero {
                background: linear-gradient(135deg, #0b1f3b, #173f74);
                color: #fff;
                border-radius: 18px;
            }
            .stat-card {
                border: 0;
                border-radius: 14px;
                color: #fff;
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
        $user = Auth::user();
        $unreadNotifications = $user?->unreadNotifications()->count() ?? 0;
        $stats = [
            [
                'label' => __('Total reports'),
                'value' => $metrics['total_reports'] ?? ($totalReports ?? 0),
                'icon' => 'fa-clipboard-list',
                'bg' => 'bg-primary',
            ],
            [
                'label' => __('Open reports'),
                'value' => $metrics['open_reports'] ?? ($openReports ?? 0),
                'icon' => 'fa-folder-open',
                'bg' => 'bg-success',
            ],
            [
                'label' => __('Urgent reports'),
                'value' => $metrics['urgent_reports'] ?? ($urgentReports ?? 0),
                'icon' => 'fa-triangle-exclamation',
                'bg' => 'bg-danger',
            ],
            [
                'label' => __('Users'),
                'value' => $metrics['users'] ?? ($userCount ?? 0),
                'icon' => 'fa-users',
                'bg' => 'bg-info',
            ],
        ];
    @endphp

    <div class="dash-hero p-4 p-md-5 mb-4 shadow-sm">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <div class="text-uppercase small opacity-75 mb-1">{{ __('Welcome back') }}</div>
                <h2 class="fw-semibold mb-2">{{ $user?->name ?? __('User') }}</h2>
                <p class="mb-0 opacity-75">{{ __("Keep tabs on reports, urgency, and team access from one place.") }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('reviews.index') }}" class="btn btn-light text-primary fw-semibold">
                    <i class="fa-solid fa-list-check me-2"></i>{{ __('Open reports') }}
                </a>
                <a href="{{ route('admin.analytics') }}" class="btn btn-outline-light">
                    <i class="fa-solid fa-chart-line me-2"></i>{{ __('View analytics') }}
                </a>
                @if (auth()->user()?->hasRole(['org_admin', 'platform_admin']))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-light">
                        <i class="fa-solid fa-user-gear me-2"></i>{{ __('Manage users') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach ($stats as $stat)
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card shadow-sm {{ $stat['bg'] }}">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="stat-icon">
                                <i class="fa-solid {{ $stat['icon'] }}"></i>
                            </div>
                            <span class="fw-semibold">{{ $stat['label'] }}</span>
                        </div>
                        <div>
                            <div class="display-6 fw-bold">{{ $stat['value'] }}</div>
                            <small class="opacity-75">{{ __('As of now') }}</small>
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
                        <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-bell me-2"></i>{{ __('Notifications') }}
                            @if ($unreadNotifications > 0)
                                <span class="badge bg-danger ms-1">{{ $unreadNotifications }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.analytics') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-chart-pie me-2"></i>{{ __('Trends') }}
                        </a>
                        @if (auth()->user()?->hasRole(['org_admin', 'platform_admin']))
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-user-plus me-2"></i>{{ __('Invite user') }}
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
