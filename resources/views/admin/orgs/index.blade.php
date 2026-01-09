<x-admin-layout>
    <x-slot name="header">
        {{ __('Organizations') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Organizations') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Manage organizations available on the platform.') }}</p>
            </div>
            @if (auth()->user()?->hasRole('platform_admin'))
                <a href="{{ route('admin.orgs.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> {{ __('Create Organization') }}
                </a>
            @endif
        </div>

        <div class="card admin-index-card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.orgs.index') }}" class="admin-filter-bar mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="sr-only" for="search-orgs">{{ __('Search organizations') }}</label>
                            <input type="search" name="q" id="search-orgs" value="{{ $search }}" class="form-control"
                                placeholder="{{ __('Search name or slug') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="sr-only" for="status-filter">{{ __('Status') }}</label>
                            <select name="status" id="status-filter" class="form-control">
                                <option value="">{{ __('All statuses') }}</option>
                                <option value="active" @selected($status === 'active')>{{ __('Active') }}</option>
                                <option value="inactive" @selected($status === 'inactive')>{{ __('Inactive') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <button type="submit" class="btn btn-outline-primary me-1">
                                <i class="fas fa-filter mr-1"></i> {{ __('Filter') }}
                            </button>
                            @if ($status !== '' || $search !== '')
                                <a href="{{ route('admin.orgs.index') }}" class="btn btn-light">
                                    {{ __('Clear') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col">{{ __('Name') }}</th>
                                <th scope="col">{{ __('Status') }}</th>
                                <th scope="col">{{ __('Public link') }}</th>
                                <th scope="col" class="text-nowrap text-end">{{ __('Created') }}</th>
                                <th scope="col" class="text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orgs as $org)
                                <tr>
                                    <td class="text-muted">#{{ $org->id }}</td>
                                    <td class="font-weight-bold">{{ $org->name }}</td>
                                    <td>
                                        @if ($org->status === 'active')
                                            <span class="badge badge-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($org->org_code)
                                            <div class="small">
                                                <a href="{{ $org->reportUrl(true) }}" target="_blank" rel="noopener">
                                                    {{ $org->reportUrl() }}
                                                </a>
                                                <button type="button" class="btn btn-light btn-sm ml-1"
                                                    onclick="navigator.clipboard.writeText('{{ $org->reportUrl(true) }}')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted">{{ __('Pending') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap text-end">{{ $org->created_at?->format('M d, Y H:i') }}</td>
                                    <td class="text-right">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.orgs.edit', $org) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if (auth()->user()?->hasRole('platform_admin'))
                                                <form action="{{ route('admin.orgs.destroy', $org) }}" method="POST"
                                                    data-swal-confirm
                                                    data-swal-title="{{ __('Delete organization') }}"
                                                    data-swal-message="{{ __('Delete this organization? This cannot be undone.') }}"
                                                    data-swal-confirm-button="{{ __('Yes, delete') }}"
                                                    data-swal-icon="warning">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        {{ __('No organizations found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($orgs->hasPages())
                    <div class="pt-3">
                        {{ $orgs->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
