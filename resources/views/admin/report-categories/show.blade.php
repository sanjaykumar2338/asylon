@php
    $canManageCategories = $canManageCategories ?? (auth()->user()?->can('manage-categories') ?? false);
@endphp

<x-admin-layout>
    <x-slot name="header">
        {{ __('Manage Subcategories') }}
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

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ __('Please review the highlighted fields.') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="container-fluid">
        <div class="hero-panel p-4 p-md-5 mb-4 shadow-sm">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="text-uppercase small opacity-75 mb-1">{{ __('Report category') }}</div>
                    <h2 class="fw-semibold mb-2">{{ $category->name }}</h2>
                    <p class="mb-0 opacity-75">{{ __('Manage subcategories and ordering for this category.') }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @if ($category->is_hidden)
                        <span class="badge bg-secondary text-dark px-3 py-2">{{ __('Hidden') }}</span>
                    @endif
                    <span class="badge bg-light text-dark px-3 py-2">{{ __('ID:') }} {{ $category->id }}</span>
                    <span class="badge bg-info text-dark px-3 py-2">{{ __('Subcategories:') }} {{ $category->subcategories->count() }}</span>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-8">
                <div class="card card-lite shadow-sm">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
                            <h5 class="mb-0 d-flex align-items-center gap-2">
                                <i class="fas fa-layer-group"></i>{{ $category->name }}
                            </h5>
                            <div class="d-flex flex-wrap mt-3 mt-md-0 gap-2">
                                @if ($canManageCategories)
                                    <a href="{{ route('admin.report-categories.edit', $category) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-edit mr-1"></i>{{ __('Edit category') }}
                                    </a>
                                @endif
                                <a href="{{ route('admin.report-categories.index') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-arrow-left mr-1"></i>{{ __('Back to categories') }}
                                </a>
                            </div>
                        </div>
                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted text-sm text-uppercase">{{ __('Description') }}</dt>
                            <dd class="col-sm-8">{{ $category->description ?: __('Not provided') }}</dd>

                            <dt class="col-sm-4 text-muted text-sm text-uppercase">{{ __('Display order') }}</dt>
                            <dd class="col-sm-8">{{ $category->position }}</dd>

                            <dt class="col-sm-4 text-muted text-sm text-uppercase">{{ __('Total subcategories') }}</dt>
                            <dd class="col-sm-8">{{ $category->subcategories->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (! $canManageCategories)
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-1"></i>{{ __('You can view subcategories, but only platform or executive admins can make changes.') }}
        </div>
    @endif

    @php
        $newSubcategoryPosition = ($category->subcategories->max('position') ?? 0) + 1;
        $editingSubcategoryId = old('subcategory_id');
        if ($editingSubcategoryId === '') {
            $editingSubcategoryId = null;
        }
        $isEditingContext = $editingSubcategoryId !== null;
        $createErrors = $isEditingContext ? null : ($errors->any() ? $errors : null);
    @endphp

    <div class="card card-lite shadow-sm">
        <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between">
            <h3 class="card-title mb-0">
                <i class="fas fa-sitemap mr-2"></i>{{ __('Subcategories') }}
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" style="width: 70px;">#</th>
                            <th scope="col">{{ __('Name') }}</th>
                            <th scope="col">{{ __('Description') }}</th>
                            <th scope="col" class="text-center" style="width: 140px;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($category->subcategories as $subcategory)
                            @php
                                $collapseId = "subcategory-edit-{$subcategory->id}";
                                $hasRowErrors = $isEditingContext && (string) $editingSubcategoryId === (string) $subcategory->id;
                                $shouldExpand = $hasRowErrors;
                                $rowErrors = $hasRowErrors ? $errors : null;
                            @endphp
                            <tr>
                                <td>{{ $subcategory->position }}</td>
                                <td>{{ $subcategory->name }}</td>
                                <td>{{ $subcategory->description ?: __('Not provided') }}</td>
                                <td class="text-center">
                                    @if ($canManageCategories)
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-secondary" type="button"
                                                data-toggle="collapse" data-target="#{{ $collapseId }}"
                                                data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}"
                                                aria-expanded="{{ $shouldExpand ? 'true' : 'false' }}">
                                                <i class="fas fa-edit mr-1"></i>{{ __('Edit') }}
                                            </button>
                                            <form method="POST" action="{{ route('admin.report-categories.subcategories.destroy', [$category, $subcategory]) }}"
                                                class="d-inline-block ml-1"
                                                data-swal-confirm
                                                data-swal-title="{{ __('Delete subcategory') }}"
                                                data-swal-message="{{ __('Delete this subcategory? This cannot be undone.') }}"
                                                data-swal-confirm-button="{{ __('Yes, delete') }}"
                                                data-swal-icon="warning">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">
                                                    <i class="fas fa-trash mr-1"></i>{{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted">{{ __('View only') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @if ($canManageCategories)
                                <tr class="collapse {{ $shouldExpand ? 'show' : '' }}" id="{{ $collapseId }}">
                                    <td colspan="4">
                                        <div class="card card-lite shadow-sm border-0">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="mb-0 d-flex align-items-center gap-2">
                                                        <i class="fas fa-pen"></i> {{ __('Edit subcategory') }}
                                                    </h6>
                                                    <span class="badge bg-light text-dark">#{{ $subcategory->position }}</span>
                                                </div>
                                                <form method="POST" action="{{ route('admin.report-categories.subcategories.update', [$category, $subcategory]) }}" class="row g-3">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="subcategory_id" value="{{ $subcategory->id }}">
                                                    <div class="col-md-4">
                                                        <label for="edit-name-{{ $subcategory->id }}" class="fw-semibold small text-uppercase text-muted">{{ __('Name') }}</label>
                                                        @php
                                                            $nameError = $rowErrors?->first('name');
                                                        @endphp
                                                        <input type="text" id="edit-name-{{ $subcategory->id }}" name="name"
                                                            class="form-control form-control-sm {{ $nameError ? 'is-invalid' : '' }}"
                                                            value="{{ $hasRowErrors ? old('name') : $subcategory->name }}"
                                                            maxlength="150" required>
                                                        @if ($nameError)
                                                            <div class="invalid-feedback">{{ $nameError }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="edit-description-{{ $subcategory->id }}" class="fw-semibold small text-uppercase text-muted">{{ __('Description (optional)') }}</label>
                                                        @php
                                                            $descriptionError = $rowErrors?->first('description');
                                                        @endphp
                                                        <input type="text" id="edit-description-{{ $subcategory->id }}" name="description"
                                                            class="form-control form-control-sm {{ $descriptionError ? 'is-invalid' : '' }}"
                                                            value="{{ $hasRowErrors ? old('description') : $subcategory->description }}"
                                                            maxlength="255"
                                                            placeholder="{{ __('Helper text for reviewers') }}">
                                                        @if ($descriptionError)
                                                            <div class="invalid-feedback">{{ $descriptionError }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="edit-position-{{ $subcategory->id }}" class="fw-semibold small text-uppercase text-muted">{{ __('Order') }}</label>
                                                        @php
                                                            $positionError = $rowErrors?->first('position');
                                                        @endphp
                                                        <input type="number" id="edit-position-{{ $subcategory->id }}" name="position"
                                                            class="form-control form-control-sm {{ $positionError ? 'is-invalid' : '' }}"
                                                            value="{{ $hasRowErrors ? old('position') : $subcategory->position }}"
                                                            min="0" max="65535">
                                                        @if ($positionError)
                                                            <div class="invalid-feedback">{{ $positionError }}</div>
                                                        @endif
                                                        <small class="text-muted">{{ __('Lower shows first') }}</small>
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-end justify-content-end">
                                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                                            <i class="fas fa-save mr-1"></i>{{ __('Update') }}
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    {{ __('No subcategories found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($canManageCategories)
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">{{ __('Add subcategory') }}</h5>
                    <span class="badge bg-light text-dark">{{ __('New entry') }}</span>
                </div>
                <form method="POST" action="{{ route('admin.report-categories.subcategories.store', $category) }}" class="row g-3">
                    @csrf
                    <input type="hidden" name="subcategory_id" value="">
                    <div class="col-md-4">
                        <label for="new-name" class="fw-semibold">{{ __('Name') }}</label>
                        @php
                            $createNameError = $createErrors?->first('name');
                        @endphp
                        <input type="text" id="new-name" name="name" class="form-control {{ $createNameError ? 'is-invalid' : '' }}"
                            value="{{ $createErrors ? old('name') : '' }}" maxlength="150" required>
                        @if ($createNameError)
                            <div class="invalid-feedback">{{ $createNameError }}</div>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label for="new-description" class="fw-semibold">{{ __('Description (optional)') }}</label>
                        @php
                            $createDescriptionError = $createErrors?->first('description');
                        @endphp
                        <input type="text" id="new-description" name="description"
                            class="form-control {{ $createDescriptionError ? 'is-invalid' : '' }}"
                            value="{{ $createErrors ? old('description') : '' }}" maxlength="255"
                            placeholder="{{ __('Helper text for reviewers') }}">
                        @if ($createDescriptionError)
                            <div class="invalid-feedback">{{ $createDescriptionError }}</div>
                        @endif
                    </div>
                    <div class="col-md-2">
                        <label for="new-position" class="fw-semibold">{{ __('Order') }}</label>
                        @php
                            $createPositionError = $createErrors?->first('position');
                        @endphp
                        <input type="number" id="new-position" name="position"
                            class="form-control {{ $createPositionError ? 'is-invalid' : '' }}"
                            value="{{ $createErrors ? old('position', $newSubcategoryPosition) : $newSubcategoryPosition }}"
                            min="0" max="65535">
                        @if ($createPositionError)
                            <div class="invalid-feedback">{{ $createPositionError }}</div>
                        @endif
                        <small class="text-muted">{{ __('Lower appears first') }}</small>
                    </div>
                    <div class="col-md-2 d-flex align-items-end justify-content-end">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-plus mr-1"></i>{{ __('Add') }}
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="card-footer text-muted">
                <i class="fas fa-info-circle mr-1"></i>{{ __('Contact a platform or executive admin to add or edit subcategories.') }}
            </div>
        @endif
    </div>
</x-admin-layout>
