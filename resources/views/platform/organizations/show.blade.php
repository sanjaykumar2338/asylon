<x-admin-layout>
    <x-slot name="header">
        {{ __('Organization') }}: {{ $org->name }}
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
                min-height: 130px;
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
        $status = $org->billing_status ?? 'pending';
        $statusClass = match ($status) {
            'active' => 'success',
            'trialing' => 'info',
            'suspended' => 'secondary',
            default => 'secondary',
        };
        $planName = $org->plan?->name ?? __('No plan');
        $reportsMonth = $org->reports_this_month_label ?? $org->reports_this_month ?? 0;
        $reportsTotal = number_format($org->total_reports ?? 0);
        $seats = $org->seats_used_label ?? $org->seats_used ?? 0;
    @endphp

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="hero-panel p-4 p-md-5 mb-4 shadow-sm">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="text-uppercase small opacity-75 mb-1">{{ __('Organization overview') }}</div>
                    <h2 class="fw-semibold mb-2">{{ $org->name }}</h2>
                    <p class="mb-0 opacity-75">{{ __('View details, plan, and usage for this organization.') }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-{{ $statusClass }} text-uppercase px-3 py-2">{{ ucfirst($status) }}</span>
                    <span class="badge bg-light text-dark px-3 py-2">{{ $planName }}</span>
                    @if ($org->trial_ends_at)
                        <span class="badge bg-warning text-dark px-3 py-2">{{ __('Trial ends') }} {{ $org->trial_ends_at->format('M d, Y') }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-sm-4 col-xl-4">
                <div class="card stat-card shadow-sm bg-primary">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="stat-icon">
                                <i class="fa-solid fa-credit-card"></i>
                            </span>
                            <span class="fw-semibold">{{ __('Plan') }}</span>
                        </div>
                        <div>
                            <div class="h4 fw-bold mb-1">{{ $planName }}</div>
                            <span class="opacity-75 small">{{ __('Current subscription plan') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-xl-4">
                <div class="card stat-card shadow-sm bg-success">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="stat-icon">
                                <i class="fa-solid fa-file-lines"></i>
                            </span>
                            <span class="fw-semibold">{{ __('Reports this month') }}</span>
                        </div>
                        <div>
                            <div class="h4 fw-bold mb-1">{{ $reportsMonth }}</div>
                            <span class="opacity-75 small">{{ __('Monthly volume') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-xl-4">
                <div class="card stat-card shadow-sm bg-info">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="stat-icon">
                                <i class="fa-solid fa-users"></i>
                            </span>
                            <span class="fw-semibold">{{ __('Seats used') }}</span>
                        </div>
                        <div>
                            <div class="h4 fw-bold mb-1">{{ $seats }}</div>
                            <span class="opacity-75 small">{{ __('Licensed seats in use') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-6">
                <div class="card card-lite shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle mr-2"></i> {{ __('Details') }}
                            </h5>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="text-muted small text-uppercase">{{ __('Name') }}</div>
                                <div>{{ $org->name }}</div>
                                <div class="text-muted small">{{ $org->short_name }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="text-muted small text-uppercase">{{ __('Type') }}</div>
                                <div>{{ $org->org_type ?? '—' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="text-muted small text-uppercase">{{ __('Contact') }}</div>
                                <div>{{ $org->contact_email ?? '—' }}</div>
                                <div class="text-muted small">{{ $org->contact_phone ?? '' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="text-muted small text-uppercase">{{ __('Created') }}</div>
                                <div>{{ $org->created_at?->format('Y-m-d') }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="text-muted small text-uppercase">{{ __('Billing Status') }}</div>
                                <div class="text-capitalize">{{ $org->billing_status ?? '—' }}</div>
                                @if ($org->billing_status === 'trialing' && $org->trial_ends_at)
                                    <div class="small text-muted mt-1">
                                        {{ __('Trial ends:') }} {{ $org->trial_ends_at->format('M d, Y') }}
                                        @if (! is_null($org->trial_days_left))
                                            <br>{{ __('Days left:') }} {{ $org->trial_days_left }}
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="text-muted small text-uppercase">{{ __('Trial Ends') }}</div>
                                <div>{{ $org->trial_ends_at ? $org->trial_ends_at->format('M d, Y') : '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card card-lite shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-line mr-2"></i> {{ __('Usage & Plan') }}
                            </h5>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="text-muted small text-uppercase">{{ __('Plan') }}</div>
                                <div>{{ $org->plan?->name ?? '—' }}</div>
                                <form method="POST" action="{{ route('platform.organizations.update_plan', $org) }}" class="mt-2">
                                    @csrf
                                    <div class="input-group">
                                        <select name="plan_id" class="form-control form-control-sm">
                                            @foreach ($plans as $plan)
                                                <option value="{{ $plan->id }}" @selected($org->plan_id == $plan->id)>{{ $plan->name }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-outline-primary btn-sm" type="submit">{{ __('Update') }}</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="text-muted small text-uppercase">{{ __('Billing Status') }}</div>
                                <div class="text-capitalize">{{ $org->billing_status ?? '—' }}</div>
                                <form method="POST" action="{{ route('platform.organizations.update_status', $org) }}" class="mt-2">
                                    @csrf
                                    <div class="input-group">
                                        <select name="billing_status" class="form-control form-control-sm">
                                            @foreach (['active','trialing','suspended'] as $status)
                                                <option value="{{ $status }}" @selected($org->billing_status === $status)>{{ Str::headline($status) }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-outline-primary btn-sm" type="submit">{{ __('Update') }}</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="text-muted small text-uppercase">{{ __('Reports This Month') }}</div>
                                <div>{{ $org->reports_this_month_label ?? $org->reports_this_month }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="text-muted small text-uppercase">{{ __('Total Reports') }}</div>
                                <div>{{ $reportsTotal }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="text-muted small text-uppercase">{{ __('Seats Used') }}</div>
                                <div>{{ $seats }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
