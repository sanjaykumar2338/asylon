<x-admin-layout>
    <x-slot name="header">
        {{ __('Demo Requests') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="card card-outline card-primary">
        <div class="card-header">
            <form method="GET" action="{{ route('admin.demo-requests.index') }}" class="w-100">
                <div class="form-row align-items-center">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <label class="sr-only" for="demo-search">{{ __('Search') }}</label>
                        <input type="search" id="demo-search" name="q" value="{{ $search }}"
                            class="form-control" placeholder="{{ __('Search name, email, or organization') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-outline-primary btn-block">
                            <i class="fas fa-filter mr-1"></i> {{ __('Filter') }}
                        </button>
                        @if ($search !== '')
                            <a href="{{ route('admin.demo-requests.index') }}" class="btn btn-link btn-sm">
                                {{ __('Clear') }}
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
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
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.demo-requests.show', $demo) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.demo-requests.destroy', $demo) }}" method="POST"
                                            data-swal-confirm
                                            data-swal-title="{{ __('Delete demo request') }}"
                                            data-swal-message="{{ __('Delete this submission?') }}"
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
                                <td colspan="7" class="text-center text-muted py-4">
                                    {{ __('No demo requests yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            {{ $demoRequests->links('pagination::bootstrap-4') }}
        </div>
    </div>
</x-admin-layout>
