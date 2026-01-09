<x-admin-layout>
    <x-slot name="header">
        {{ __('Blog Categories') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Blog Categories') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Create and edit categories for organizing blog posts.') }}</p>
            </div>
            <span class="badge badge-info badge-pill px-3 py-2">
                {{ __('Total') }}: {{ $categories->count() }}
            </span>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="card admin-index-card h-100">
                    <div class="card-body">
                        <h2 class="h6 mb-3 text-uppercase text-muted">{{ __('Add Category') }}</h2>
                        <form action="{{ route('admin.blog-categories.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>{{ __('Name') }}</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>{{ __('Slug') }} ({{ __('optional') }})</label>
                                <input type="text" name="slug" class="form-control" placeholder="{{ __('auto-generated') }}">
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-save mr-1"></i> {{ __('Save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-3">
                <div class="card admin-index-card h-100">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Slug') }}</th>
                                        <th class="text-right text-nowrap" style="width: 180px;">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categories as $category)
                                        <tr>
                                            <td class="font-weight-bold">{{ $category->name }}</td>
                                            <td><code>{{ $category->slug }}</code></td>
                                            <td class="text-right">
                                                <div class="d-flex flex-wrap justify-content-end align-items-center" style="gap: 0.5rem;">
                                                    <button class="btn btn-outline-primary btn-sm" type="button"
                                                        data-toggle="collapse" data-target="#edit-cat-{{ $category->id }}"
                                                        data-bs-toggle="collapse" data-bs-target="#edit-cat-{{ $category->id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form action="{{ route('admin.blog-categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Delete category?') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="collapse bg-light" id="edit-cat-{{ $category->id }}">
                                            <td colspan="3">
                                                <div class="p-3">
                                                    <form action="{{ route('admin.blog-categories.update', $category) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="small text-muted">{{ __('Name') }}</label>
                                                                <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="small text-muted">{{ __('Slug') }}</label>
                                                                <input type="text" name="slug" class="form-control" value="{{ $category->slug }}">
                                                            </div>
                                                        </div>
                                                        <div class="d-flex justify-content-end mt-3">
                                                            <button type="submit" class="btn btn-primary btn-sm px-3 py-2">
                                                                <i class="fas fa-save mr-1"></i> {{ __('Update') }}
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">{{ __('No categories yet.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
