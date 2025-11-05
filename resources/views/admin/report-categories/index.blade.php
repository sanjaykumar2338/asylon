<x-admin-layout>
    <x-slot name="header">
        {{ __('Report Categories') }}
    </x-slot>

    @section('breadcrumb')
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Report Categories') }}</li>
        </ol>
    @endsection
    <div class="card card-outline card-primary">
        <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between">
            <h3 class="card-title mb-0">
                <i class="fas fa-layer-group mr-2"></i>{{ __('Categories') }}
            </h3>
            <a href="{{ route('admin.report-categories.create') }}" class="btn btn-primary btn-sm mt-3 mt-md-0">
                <i class="fas fa-plus mr-1"></i>{{ __('Add Category') }}
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" style="width: 80px;">#</th>
                            <th scope="col">{{ __('Name') }}</th>
                            <th scope="col">{{ __('Description') }}</th>
                            <th scope="col" class="text-center" style="width: 140px;">{{ __('Subcategories') }}</th>
                            <th scope="col" class="text-center" style="width: 220px;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{ $category->position }}</td>
                                <td class="font-weight-semibold">{{ $category->name }}</td>
                                <td>{{ $category->description ?: '—' }}</td>
                                <td class="text-center">
                                    <span class="badge badge-info badge-pill">{{ $category->subcategories_count }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.report-categories.show', $category) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-sitemap mr-1"></i>{{ __('Manage Subcategories') }}
                                        </a>
                                        <a href="{{ route('admin.report-categories.edit', $category) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-edit mr-1"></i>{{ __('Edit') }}
                                        </a>
                                        <form method="POST" action="{{ route('admin.report-categories.destroy', $category) }}"
                                            class="d-inline-block ml-1"
                                            data-swal-confirm
                                            data-swal-title="{{ __('Delete category') }}"
                                            data-swal-message="{{ __('Are you sure you want to delete this category? This cannot be undone.') }}"
                                            data-swal-confirm-button="{{ __('Yes, delete') }}"
                                            data-swal-icon="warning">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="fas fa-trash mr-1"></i>{{ __('Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    {{ __('No categories configured yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
