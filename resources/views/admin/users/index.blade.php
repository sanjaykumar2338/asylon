<x-admin-layout>
    <x-slot name="header">
        {{ __('Users') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Users') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Manage platform and organization users.') }}</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus mr-2"></i> {{ __('Invite User') }}
            </a>
        </div>

        <div class="card admin-index-card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.users.index') }}" class="admin-filter-bar mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="sr-only" for="search-users">{{ __('Search users') }}</label>
                            <div class="input-group">
                                <input type="search" name="q" id="search-users" value="{{ $search }}" class="form-control"
                                    placeholder="{{ __('Search name or email') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search mr-1"></i> {{ __('Search') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        @if(auth()->user()?->hasRole('platform_admin'))
                            <div class="col-md-4">
                                <label class="sr-only" for="org_id">{{ __('Organization') }}</label>
                                <select id="org_id" name="org_id" class="form-control">
                                    <option value="0">{{ __('All organizations') }}</option>
                                    @foreach(($orgOptions ?? collect()) as $org)
                                        <option value="{{ $org->id }}" @selected((string)$orgId === (string)$org->id)>{{ $org->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-md-3 text-md-end d-flex flex-wrap justify-content-md-end align-items-center gap-2">
                            @if ($search !== '' || (auth()->user()?->hasRole('platform_admin') && $orgId))
                                <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm">
                                    {{ __('Clear') }}
                                </a>
                            @endif
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="fas fa-filter mr-1"></i> {{ __('Filter') }}
                            </button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
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

                @if ($users->hasPages())
                    <div class="pt-3">
                        {{ $users->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
