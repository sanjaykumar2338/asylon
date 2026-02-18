<x-admin-layout>
    <x-slot name="header">
        {{ __('Demo Requests') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Demo Requests') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Review inbound demo submissions and follow up quickly.') }}</p>
            </div>
            <span class="badge badge-info badge-pill px-3 py-2">
                {{ __('Total') }}: {{ $demoRequests->total() }}
            </span>
        </div>

        <div class="card admin-index-card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.demo-requests.index') }}" class="admin-filter-bar mb-0">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-6">
                            <label class="sr-only" for="demo-search">{{ __('Search') }}</label>
                            <input type="search" id="demo-search" name="q" value="{{ $search }}"
                                class="form-control" placeholder="{{ __('Search name, email, or organization') }}">
                        </div>
                        <div class="col-md-3 text-md-end">
                            <button type="submit" class="btn btn-outline-primary mr-1">
                                <i class="fas fa-filter mr-1"></i> {{ __('Filter') }}
                            </button>
                            @if ($search !== '')
                                <a href="{{ route('admin.demo-requests.index') }}" class="btn btn-light btn-sm">
                                    {{ __('Clear') }}
                                </a>
                            @endif
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
                                <th scope="col">{{ __('Email') }}</th>
                                <th scope="col">{{ __('Organization') }}</th>
                                <th scope="col">{{ __('Type') }}</th>
                                <th scope="col">{{ __('Meeting') }}</th>
                                <th scope="col">{{ __('Submitted') }}</th>
                                <th scope="col" class="text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($demoRequests as $demo)
                                <tr>
                                    <td class="font-weight-bold">{{ $demo->first_name }} {{ $demo->last_name }}</td>
                                    <td>{{ $demo->email }}</td>
                                    <td>{{ $demo->organization }}</td>
                                    <td>{{ $demo->organization_type ?? '—' }}</td>
                                    <td>{{ $demo->meeting ?? '—' }}</td>
                                    <td>{{ $demo->created_at?->format('M d, Y g:i A') }}</td>
                                    <td class="text-right">
                                        <div class="d-flex flex-wrap justify-content-end align-items-center" style="gap: 0.5rem;">
                                            <a href="{{ route('admin.demo-requests.show', $demo) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.demo-requests.destroy', $demo) }}" method="POST"
                                                class="d-inline"
                                                data-swal-confirm
                                                data-swal-title="{{ __('Delete demo request') }}"
                                                data-swal-message="{{ __('Delete this submission?') }}"
                                                data-swal-confirm-button="{{ __('Yes, delete') }}"
                                                data-swal-icon="warning">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        {{ __('No demo requests yet.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($demoRequests->hasPages())
                <div class="pt-3 px-3">
                    {{ $demoRequests->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
