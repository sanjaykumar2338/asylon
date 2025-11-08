<x-admin-layout>
    <x-slot name="header">
        {{ __('Alert Contacts') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="card card-outline card-primary">
        <div class="card-header">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
                <form method="GET" action="{{ route('admin.alerts.index') }}" class="w-100 mb-3 mb-lg-0">
                    <div class="form-row">
                        <div class="col-md-5 mb-2 mb-md-0">
                            <label class="sr-only" for="search-alerts">{{ __('Search contacts') }}</label>
                            <input type="search" name="q" id="search-alerts" value="{{ $search }}" class="form-control"
                                placeholder="{{ __('Search contact value') }}">
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="sr-only" for="type-filter">{{ __('Type') }}</label>
                            <select name="type" id="type-filter" class="form-control">
                                <option value="">{{ __('All types') }}</option>
                                <option value="email" @selected($type === 'email')>{{ __('Email') }}</option>
                                <option value="sms" @selected($type === 'sms')>{{ __('SMS') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 text-md-right">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-filter mr-1"></i> {{ __('Filter') }}
                            </button>
                            @if ($type !== '' || $search !== '')
                                <a href="{{ route('admin.alerts.index') }}" class="btn btn-link">
                                    {{ __('Clear') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <a href="{{ route('admin.alerts.create') }}" class="btn btn-primary ml-lg-3">
                    <i class="fas fa-plus mr-1"></i> {{ __('Add Contact') }}
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
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
        </div>

        <div class="card-footer">
            {{ $alerts->links('pagination::bootstrap-4') }}
        </div>
    </div>
</x-admin-layout>
