<x-admin-layout>
    <x-slot name="header">
        {{ __('Users') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="card card-outline card-primary">
        <div class="card-header">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
                <form method="GET" action="{{ route('admin.users.index') }}" class="form-inline flex-grow-1 mb-3 mb-lg-0">
                    <label class="sr-only" for="search-users">{{ __('Search users') }}</label>
                    <div class="input-group w-100">
                        <input type="search" name="q" id="search-users" value="{{ $search }}" class="form-control"
                            placeholder="{{ __('Search name or email') }}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search mr-1"></i> {{ __('Search') }}
                            </button>
                        </div>
                    </div>
                    @if ($search !== '')
                        <a href="{{ route('admin.users.index') }}" class="btn btn-link btn-sm ml-0 ml-lg-2 mt-2 mt-lg-0">
                            {{ __('Clear') }}
                        </a>
                    @endif
                </form>

                <a href="{{ route('admin.users.create') }}" class="btn btn-primary ml-lg-3">
                    <i class="fas fa-user-plus mr-2"></i> {{ __('Invite User') }}
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">{{ __('Name') }}</th>
                            <th scope="col">{{ __('Email') }}</th>
                            <th scope="col">{{ __('Role') }}</th>
                            <th scope="col">{{ __('Organization') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                            <th scope="col" class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td class="font-weight-bold">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td class="text-capitalize">{{ str_replace('_', ' ', $user->role) }}</td>
                                <td>{{ $user->org?->name ?? __('Unassigned') }}</td>
                                <td>
                                    @if ($user->active)
                                        <span class="badge badge-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                            data-swal-confirm
                                            data-swal-title="{{ __('Delete user') }}"
                                            data-swal-message="{{ __('Delete this user?') }}"
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
                                    {{ __('No users found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            {{ $users->links('pagination::bootstrap-4') }}
        </div>
    </div>
</x-admin-layout>
