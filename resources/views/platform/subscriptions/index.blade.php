<x-admin-layout>
    <x-slot name="header">
        {{ __('Subscriptions') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Subscriptions') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Platform-level view of all organization subscriptions.') }}</p>
            </div>
            <span class="badge badge-info badge-pill px-3 py-2">{{ $orgs->total() }} {{ __('total') }}</span>
        </div>

        <div class="card admin-index-card card-outline card-primary mb-4">
            <div class="card-body">
                <form method="GET" class="admin-filter-bar mb-0">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="search">{{ __('Search') }}</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ $search }}" placeholder="{{ __('Org name or email') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="status">{{ __('Status') }}</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">{{ __('All') }}</option>
                                @foreach (['active', 'past_due', 'canceled', 'pending'] as $s)
                                    <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="plan">{{ __('Plan') }}</label>
                            <select name="plan" id="plan" class="form-control">
                                <option value="">{{ __('All plans') }}</option>
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->slug }}" @selected($planSlug === $plan->slug)>{{ $plan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="renewal_start">{{ __('Renewal start') }}</label>
                            <input type="date" name="renewal_start" id="renewal_start" class="form-control" value="{{ optional($renewalStart)->toDateString() }}">
                        </div>
                        <div class="col-md-3">
                            <label for="renewal_end">{{ __('Renewal end') }}</label>
                            <input type="date" name="renewal_end" id="renewal_end" class="form-control" value="{{ optional($renewalEnd)->toDateString() }}">
                        </div>
                        <div class="col-12 d-flex justify-content-end align-items-end mt-3">
                            <a href="{{ route('platform.billing.subscriptions.index') }}" class="btn btn-outline-secondary mr-2">
                                <i class="fas fa-undo mr-1"></i>{{ __('Reset') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search mr-1"></i>{{ __('Apply') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title mb-0"><i class="fas fa-stream mr-2"></i>{{ __('Subscriptions') }}</h3>
                    <small class="text-muted">{{ __('Organization subscriptions with renewal dates') }}</small>
                </div>
                <span class="badge badge-light">{{ $orgs->total() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ __('Org') }}</th>
                            <th>{{ __('Plan') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Renewal') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orgs as $org)
                            @php
                                $sub = $org->latestBillingSubscription;
                                $initials = \Illuminate\Support\Str::of($org->name ?? '')->explode(' ')->map(fn ($p) => \Illuminate\Support\Str::substr($p, 0, 1))->take(2)->implode('') ?: 'O';
                                $status = $org->billing_status ?? 'pending';
                                $statusClass = match ($status) {
                                    'active' => 'success',
                                    'past_due' => 'warning',
                                    'canceled' => 'secondary',
                                    'pending' => 'info',
                                    default => 'secondary',
                                };
                                $planSlug = $org->plan?->slug ?? $sub?->plan_slug ?? '—';
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center me-3"
                                            style="width: 44px; height: 44px; font-weight: 600;">
                                            {{ strtoupper($initials) }}
                                        </span>
                                        <div>
                                            <div class="font-weight-bold">{{ $org->name }}</div>
                                            <small class="text-muted">{{ $org->contact_email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-uppercase">
                                    <span class="badge badge-light border">{{ $planSlug }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $statusClass }}">{{ ucfirst($status) }}</span>
                                </td>
                                <td>
                                    @if ($sub?->current_period_end)
                                        <div class="fw-semibold">{{ $sub->current_period_end->format('M j, Y') }}</div>
                                        <small class="text-muted">{{ __('Period end') }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('platform.billing.subscriptions.show', $org) }}" class="btn btn-sm btn-outline-primary">
                                        {{ __('View') }}
                                    </a>
                                    <form method="POST" action="{{ route('platform.billing.subscriptions.sync', $org) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                            {{ __('Sync') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">{{ __('No subscriptions found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $orgs->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
