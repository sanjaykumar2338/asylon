<x-admin-layout>
    <x-slot name="header">
        {{ __('Subscriptions') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="card card-outline card-primary mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-filter mr-2"></i>{{ __('Filters') }}</h3>
            <span class="text-muted small">{{ __('Platform-level view of all organization subscriptions.') }}</span>
        </div>
        <div class="card-body">
            <form method="GET" class="form-row">
                <div class="form-group col-md-3">
                    <label for="search">{{ __('Search') }}</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ $search }}" placeholder="{{ __('Org name or email') }}">
                </div>
                <div class="form-group col-md-3">
                    <label for="status">{{ __('Status') }}</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">{{ __('All') }}</option>
                        @foreach (['active', 'past_due', 'canceled', 'pending'] as $s)
                            <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="plan">{{ __('Plan') }}</label>
                    <select name="plan" id="plan" class="form-control">
                        <option value="">{{ __('All plans') }}</option>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->slug }}" @selected($planSlug === $plan->slug)>{{ $plan->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="renewal_start">{{ __('Renewal start') }}</label>
                    <input type="date" name="renewal_start" id="renewal_start" class="form-control" value="{{ optional($renewalStart)->toDateString() }}">
                </div>
                <div class="form-group col-md-3 mt-3 mt-md-0">
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
            </form>
        </div>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-stream mr-2"></i>{{ __('Subscriptions') }}</h3>
            <span class="badge badge-light">{{ $orgs->total() }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
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
                        @endphp
                        <tr>
                            <td>
                                <div class="font-weight-bold">{{ $org->name }}</div>
                                <small class="text-muted">{{ $org->contact_email }}</small>
                            </td>
                            <td class="text-uppercase">
                                {{ $org->plan?->slug ?? $sub?->plan_slug ?? '—' }}
                            </td>
                            <td>
                                <span class="badge badge-{{ $org->billing_status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($org->billing_status ?? 'pending') }}</span>
                            </td>
                            <td>
                                @if ($sub?->current_period_end)
                                    {{ $sub->current_period_end->format('M j, Y') }}
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
</x-admin-layout>
