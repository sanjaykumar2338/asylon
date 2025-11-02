<x-admin-layout>
    <x-slot name="header">
        {{ __('Organizations') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="card card-outline card-primary">
        <div class="card-header">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
                <form method="GET" action="{{ route('admin.orgs.index') }}" class="w-100 mb-3 mb-lg-0">
                    <div class="form-row">
                        <div class="col-md-5 mb-2 mb-md-0">
                            <label class="sr-only" for="search-orgs">{{ __('Search organizations') }}</label>
                            <input type="search" name="q" id="search-orgs" value="{{ $search }}" class="form-control"
                                placeholder="{{ __('Search name or slug') }}">
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="sr-only" for="status-filter">{{ __('Status') }}</label>
                            <select name="status" id="status-filter" class="form-control">
                                <option value="">{{ __('All statuses') }}</option>
                                <option value="active" @selected($status === 'active')>{{ __('Active') }}</option>
                                <option value="inactive" @selected($status === 'inactive')>{{ __('Inactive') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 text-md-right">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-filter mr-1"></i> {{ __('Filter') }}
                            </button>
                            @if ($status !== '' || $search !== '')
                                <a href="{{ route('admin.orgs.index') }}" class="btn btn-link">
                                    {{ __('Clear') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                @if (auth()->user()?->hasRole('platform_admin'))
                    <a href="{{ route('admin.orgs.create') }}" class="btn btn-primary ml-lg-3">
                        <i class="fas fa-plus mr-1"></i> {{ __('Create Organization') }}
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body p-0">
            <div class="px-3 pt-3">
                <p class="text-muted mb-0">{{ __('Manage organizations available on the platform.') }}</p>
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{ __('Name') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                            <th scope="col">{{ __('Created') }}</th>
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
                                <td>{{ $org->created_at?->format('M d, Y H:i') }}</td>
                                <td class="text-right">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.orgs.edit', $org) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if (auth()->user()?->hasRole('platform_admin'))
                                            <form action="{{ route('admin.orgs.destroy', $org) }}" method="POST"
                                                onsubmit="return confirm('{{ __('Delete this organization? This cannot be undone.') }}');">
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
                                <td colspan="5" class="text-center text-muted py-4">
                                    {{ __('No organizations found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            {{ $orgs->links('pagination::bootstrap-4') }}
        </div>
    </div>
</x-admin-layout>
