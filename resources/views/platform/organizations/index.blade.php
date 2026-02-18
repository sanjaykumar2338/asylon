<x-admin-layout>
    <x-slot name="header">
        {{ __('Organizations') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Organizations') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Manage organizations, plans, and billing status.') }}</p>
            </div>
            <span class="badge badge-info badge-pill px-3 py-2">
                {{ __('Results') }}: {{ number_format($orgs->total()) }}
            </span>
        </div>

        <div class="card admin-index-card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('platform.organizations.index') }}" class="admin-filter-bar mb-0">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label for="search">{{ __('Search') }}</label>
                            <input type="text" id="search" name="search" class="form-control" value="{{ $search ?? '' }}" placeholder="{{ __('Name, short name, email') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="status">{{ __('Billing Status') }}</label>
                            <select id="status" name="status" class="form-control">
                                <option value="">{{ __('All') }}</option>
                                @foreach (['active','trialing','suspended'] as $option)
                                    <option value="{{ $option }}" @selected($status === $option)>{{ Str::headline($option) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="plan_id">{{ __('Plan') }}</label>
                            <select id="plan_id" name="plan" class="form-control">
                                <option value="">{{ __('All') }}</option>
                                <option value="starter" @selected(($planSlug ?? '') === 'starter')>{{ __('Starter') }}</option>
                                <option value="pro" @selected(($planSlug ?? '') === 'pro')>{{ __('Pro') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="type">{{ __('Organization Type') }}</label>
                            <select id="type" name="type" class="form-control">
                                <option value="">{{ __('All') }}</option>
                                @foreach (['school' => __('School'), 'church' => __('Church'), 'organization' => __('Organization'), 'other' => __('Other')] as $value => $label)
                                    <option value="{{ $value }}" @selected(($type ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 text-md-end">
                            <button type="submit" class="btn btn-outline-primary mr-1">
                                <i class="fas fa-filter mr-1"></i> {{ __('Apply') }}
                            </button>
                            <a href="{{ route('platform.organizations.index') }}" class="btn btn-light">
                                {{ __('Reset') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card admin-index-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">{{ __('Name') }}</th>
                                <th scope="col">{{ __('Type') }}</th>
                                <th scope="col">{{ __('Plan') }}</th>
                                <th scope="col">{{ __('Billing Status') }}</th>
                                <th scope="col">{{ __('Reports This Month') }}</th>
                                <th scope="col">{{ __('Total Reports') }}</th>
                                <th scope="col">{{ __('Seats Used') }}</th>
                                <th scope="col">{{ __('Created') }}</th>
                                <th scope="col" class="text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orgs as $org)
                                <tr>
                                    <td class="font-weight-bold">{{ $org->name }}</td>
                                    <td>{{ $org->org_type ?? '—' }}</td>
                                    <td>{{ $org->plan?->name ?? '—' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $org->billing_status === 'suspended' ? 'danger' : ($org->billing_status === 'trialing' ? 'warning' : 'success') }}">
                                            {{ Str::headline($org->billing_status ?? 'active') }}
                                        </span>
                                    </td>
                                    <td>{{ $org->reports_this_month_label ?? $org->reports_this_month }}</td>
                                    <td>{{ number_format($org->total_reports ?? 0) }}</td>
                                    <td>{{ $org->seats_used_label ?? $org->seats_used }}</td>
                                    <td>{{ $org->created_at?->format('Y-m-d') }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('platform.organizations.show', $org) }}" class="btn btn-outline-primary btn-sm">
                                            {{ __('View') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        {{ __('No organizations found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($orgs->hasPages())
                <div class="pt-3 px-3">
                    {{ $orgs->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
