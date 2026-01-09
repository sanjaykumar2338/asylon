<x-admin-layout>
    <x-slot name="header">
        {{ __('New Category') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Create Category') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Add a new report category with description and display order.') }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card admin-index-card">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>{{ __('There were some issues with your input.') }}</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.report-categories.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="name">{{ __('Name') }}</label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $category->name) }}" maxlength="150" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="description">{{ __('Description (optional)') }}</label>
                                <input type="text" id="description" name="description"
                                    class="form-control @error('description') is-invalid @enderror"
                                    value="{{ old('description', $category->description) }}" maxlength="255">
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="position">{{ __('Display order') }}</label>
                                <input type="number" id="position" name="position"
                                    class="form-control @error('position') is-invalid @enderror"
                                    value="{{ old('position', $category->position) }}" min="0" max="65535">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    {{ __('Lower numbers appear first in the list. Leave blank to add to the end.') }}
                                </small>
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.report-categories.index') }}" class="btn btn-outline-secondary px-3 py-2">
                                    {{ __('Cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary ml-2 px-3 py-2">
                                    <i class="fas fa-save mr-1"></i>{{ __('Save category') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
