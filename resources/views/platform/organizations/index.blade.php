<x-admin-layout>
    <x-slot name="header">
        {{ __('Organizations') }}
    </x-slot>

    <div class="card card-outline card-primary mb-4">
        <div class="card-header">
            <h3 class="card-title mb-0">
                <i class="fas fa-filter mr-2"></i> {{ __('Filters') }}
            </h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('platform.organizations.index') }}">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="search">{{ __('Search') }}</label>
                        <input type="text" id="search" name="search" class="form-control" value="{{ $search ?? '' }}" placeholder="{{ __('Name, short name, email') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">{{ __('Billing Status') }}</label>
                        <select id="status" name="status" class="form-control">
                            <option value="">{{ __('All') }}</option>
                            @foreach (['active','trialing','suspended'] as $option)
                                <option value="{{ $option }}" @selected($status === $option)>{{ Str::headline($option) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="plan_id">{{ __('Plan') }}</label>
                        <select id="plan_id" name="plan" class="form-control">
                            <option value="">{{ __('All') }}</option>
                            <option value="starter" @selected(($planSlug ?? '') === 'starter')>{{ __('Starter') }}</option>
                            <option value="pro" @selected(($planSlug ?? '') === 'pro')>{{ __('Pro') }}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="type">{{ __('Organization Type') }}</label>
                        <select id="type" name="type" class="form-control">
                            <option value="">{{ __('All') }}</option>
                            @foreach (['school' => __('School'), 'church' => __('Church'), 'organization' => __('Organization'), 'other' => __('Other')] as $value => $label)
                                <option value="{{ $value }}" @selected(($type ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="d-flex flex-column flex-sm-row justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary mr-sm-2 mb-2 mb-sm-0">
                        <i class="fas fa-search mr-1"></i> {{ __('Apply') }}
                    </button>
                    <a href="{{ route('platform.organizations.index') }}" class="btn btn-outline-secondary">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-building mr-2"></i> {{ __('Organizations') }}
            </h3>
            <span class="badge badge-info badge-pill px-3 py-2 mt-3 mt-md-0">
                {{ __('Results') }}: {{ number_format($orgs->total()) }}
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
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
        <div class="card-footer">
            {{ $orgs->links('pagination::bootstrap-4') }}
        </div>
    </div>
</x-admin-layout>
