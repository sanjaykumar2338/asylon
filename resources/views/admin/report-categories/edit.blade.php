<x-admin-layout>
    <x-slot name="header">
        {{ __('Edit Category') }}
    </x-slot>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <style>
            .hero-panel {
                background: linear-gradient(135deg, #0b1f3b, #173f74);
                color: #fff;
                border-radius: 18px;
            }
            .card-lite {
                border-radius: 14px;
                border: 1px solid #e5e7eb;
            }
        </style>
    @endpush

    @section('breadcrumb')
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('dashboard.breadcrumb') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.report-categories.index') }}">{{ __('Report Categories') }}</a></li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    @endsection

    <div class="container-fluid">
        <div class="hero-panel p-4 p-md-5 mb-4 shadow-sm">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="text-uppercase small opacity-75 mb-1">{{ __('Edit category') }}</div>
                    <h2 class="fw-semibold mb-2">{{ $category->name }}</h2>
                    <p class="mb-0 opacity-75">{{ __('Update title, description, and display order for this report category.') }}</p>
                </div>
                <span class="badge bg-light text-dark px-3 py-2">{{ __('ID:') }} {{ $category->id }}</span>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card card-lite shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 d-flex align-items-center gap-2">
                                <i class="fas fa-edit"></i> {{ __('Update Category') }}
                            </h5>
                            <span class="badge bg-secondary text-dark text-uppercase">Reports</span>
                        </div>

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

                        <form method="POST" action="{{ route('admin.report-categories.update', $category) }}" class="row g-3">
                            @csrf
                            @method('PUT')

                            <div class="col-12">
                                <label for="name" class="fw-semibold text-uppercase small text-muted">{{ __('Name') }}</label>
                                <input type="text" id="name" name="name" class="form-control form-control-lg @error('name') is-invalid @enderror"
                                    value="{{ old('name', $category->name) }}" maxlength="150" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="fw-semibold text-uppercase small text-muted">{{ __('Description (optional)') }}</label>
                                <input type="text" id="description" name="description"
                                    class="form-control form-control-lg @error('description') is-invalid @enderror"
                                    value="{{ old('description', $category->description) }}" maxlength="255"
                                    placeholder="{{ __('Short helper text for reviewers') }}">
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="position" class="fw-semibold text-uppercase small text-muted">{{ __('Display order') }}</label>
                                <input type="number" id="position" name="position"
                                    class="form-control form-control-lg @error('position') is-invalid @enderror"
                                    value="{{ old('position', $category->position) }}" min="0" max="65535">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    {{ __('Lower numbers appear first in the list.') }}
                                </small>
                            </div>

                            <div class="col-12 d-flex justify-content-end">
                                <a href="{{ route('admin.report-categories.index') }}" class="btn btn-outline-secondary mr-2">
                                    {{ __('Cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save mr-1"></i>{{ __('Update category') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
