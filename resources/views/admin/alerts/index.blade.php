<x-admin-layout>
    <x-slot name="header">
        {{ __('Alert Contacts') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Alert Contacts') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Manage alert contacts by type and organization.') }}</p>
            </div>
            <a href="{{ route('admin.alerts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> {{ __('Add Contact') }}
            </a>
        </div>

        <div class="card admin-index-card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.alerts.index') }}" class="admin-filter-bar mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="sr-only" for="search-alerts">{{ __('Search contacts') }}</label>
                            <input type="search" name="q" id="search-alerts" value="{{ $search }}" class="form-control"
                                placeholder="{{ __('Search contact value') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="sr-only" for="type-filter">{{ __('Type') }}</label>
                            <select name="type" id="type-filter" class="form-control">
                                <option value="">{{ __('All types') }}</option>
                                <option value="email" @selected($type === 'email')>{{ __('Email') }}</option>
                                <option value="sms" @selected($type === 'sms')>{{ __('SMS') }}</option>
                            </select>
                        </div>
                        @if(auth()->user()?->hasRole('platform_admin'))
                            <div class="col-md-3">
                                <label class="sr-only" for="org_id">{{ __('Organization') }}</label>
                                <select name="org_id" id="org_id" class="form-control">
                                    <option value="0">{{ __('All organizations') }}</option>
                                    @foreach(($orgOptions ?? collect()) as $org)
                                        <option value="{{ $org->id }}" @selected((string)($orgId ?? '') === (string)$org->id)>{{ $org->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-md-2 text-md-end d-flex flex-wrap justify-content-md-end align-items-center gap-2">
                            @if ($type !== '' || $search !== '' || ($orgId ?? 0))
                                <a href="{{ route('admin.alerts.index') }}" class="btn btn-light btn-sm">
                                    {{ __('Clear') }}
                                </a>
                            @endif
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-filter mr-1"></i> {{ __('Filter') }}
                            </button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">{{ __('Type') }}</th>
                                <th scope="col">{{ __('Department') }}</th>
                                <th scope="col">{{ __('Value') }}</th>
                                <th scope="col">{{ __('Organization') }}</th>
                                <th scope="col">{{ __('Active') }}</th>
                                <th scope="col" class="text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($alerts as $alert)
                                <tr>
                                    <td class="text-capitalize">{{ $alert->type }}</td>
                                    <td class="text-capitalize">{{ $alert->department ?? __('Unassigned') }}</td>
                                    <td class="font-weight-bold">{{ $alert->value }}</td>
                                    <td>{{ $alert->org?->name ?? __('Unassigned') }}</td>
                                    <td>
                                        @if ($alert->is_active)
                                            <span class="badge badge-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.alerts.edit', $alert) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.alerts.destroy', $alert) }}" method="POST"
                                                data-swal-confirm
                                                data-swal-title="{{ __('Delete contact') }}"
                                                data-swal-message="{{ __('Delete this contact?') }}"
                                                data-swal-confirm-button="{{ __('Yes, delete') }}"
                                                data-swal-icon="warning">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        {{ __('No alert contacts found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($alerts->hasPages())
                    <div class="pt-3">
                        {{ $alerts->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
