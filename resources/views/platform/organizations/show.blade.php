<x-admin-layout>
    <x-slot name="header">
        {{ __('Organization') }}: {{ $org->name }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="card card-outline card-primary mb-4">
        <div class="card-header">
            <h3 class="card-title mb-0">
                <i class="fas fa-info-circle mr-2"></i> {{ __('Details') }}
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="text-muted small text-uppercase">{{ __('Name') }}</div>
                    <div>{{ $org->name }}</div>
                    <div class="text-muted small">{{ $org->short_name }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="text-muted small text-uppercase">{{ __('Type') }}</div>
                    <div>{{ $org->org_type ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="text-muted small text-uppercase">{{ __('Contact') }}</div>
                    <div>{{ $org->contact_email ?? '—' }}</div>
                    <div class="text-muted small">{{ $org->contact_phone ?? '' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="text-muted small text-uppercase">{{ __('Billing Status') }}</div>
                    <div class="text-capitalize">{{ $org->billing_status }}</div>
                    @if ($org->billing_status === 'trialing' && $org->trial_ends_at)
                        <div class="small text-muted mt-1">
                            {{ __('Trial ends:') }} {{ $org->trial_ends_at->format('M d, Y') }}
                            @if (! is_null($org->trial_days_left))
                                <br>{{ __('Days left:') }} {{ $org->trial_days_left }}
                            @endif
                        </div>
                    @endif
                </div>
                <div class="col-md-4 mb-3">
                    <div class="text-muted small text-uppercase">{{ __('Trial Ends') }}</div>
                    <div>{{ $org->trial_ends_at ? $org->trial_ends_at->format('M d, Y') : '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="text-muted small text-uppercase">{{ __('Created') }}</div>
                    <div>{{ $org->created_at?->format('Y-m-d') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-primary mb-4">
        <div class="card-header">
            <h3 class="card-title mb-0">
                <i class="fas fa-chart-line mr-2"></i> {{ __('Usage & Plan') }}
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="text-muted small text-uppercase">{{ __('Plan') }}</div>
                    <div>{{ $org->plan?->name ?? '—' }}</div>
                    <form method="POST" action="{{ route('platform.organizations.update_plan', $org) }}" class="mt-2">
                        @csrf
                        <div class="input-group input-group-sm">
                            <select name="plan_id" class="form-control">
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->id }}" @selected($org->plan_id == $plan->id)>{{ $plan->name }}</option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" type="submit">{{ __('Update') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="text-muted small text-uppercase">{{ __('Billing Status') }}</div>
                    <div class="text-capitalize">{{ $org->billing_status }}</div>
                    <form method="POST" action="{{ route('platform.organizations.update_status', $org) }}" class="mt-2">
                        @csrf
                        <div class="input-group input-group-sm">
                            <select name="billing_status" class="form-control">
                                @foreach (['active','trialing','suspended'] as $status)
                                    <option value="{{ $status }}" @selected($org->billing_status === $status)>{{ Str::headline($status) }}</option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" type="submit">{{ __('Update') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="text-muted small text-uppercase">{{ __('Reports This Month') }}</div>
                    <div>{{ $org->reports_this_month_label ?? $org->reports_this_month }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="text-muted small text-uppercase">{{ __('Total Reports') }}</div>
                    <div>{{ number_format($org->total_reports ?? 0) }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="text-muted small text-uppercase">{{ __('Seats Used') }}</div>
                    <div>{{ $org->seats_used_label ?? $org->seats_used }}</div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
