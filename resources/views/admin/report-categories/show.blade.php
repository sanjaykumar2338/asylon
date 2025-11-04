<x-admin-layout>
    <x-slot name="header">
        {{ __('Manage Subcategories') }}
    </x-slot>

    @section('breadcrumb')
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.report-categories.index') }}">{{ __('Report Categories') }}</a></li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    @endsection

    @if (session('ok'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('ok') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ __('Please review the highlighted fields.') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-outline card-primary mb-4">
                <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-layer-group mr-2"></i>{{ $category->name }}
                    </h3>
                    <div class="d-flex flex-wrap mt-3 mt-md-0">
                        <a href="{{ route('admin.report-categories.edit', $category) }}" class="btn btn-outline-secondary btn-sm mr-2">
                            <i class="fas fa-edit mr-1"></i>{{ __('Edit category') }}
                        </a>
                        <a href="{{ route('admin.report-categories.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i>{{ __('Back to categories') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
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

    @php
        $newSubcategoryPosition = ($category->subcategories->max('position') ?? 0) + 1;
        $editingSubcategoryId = old('subcategory_id');
        if ($editingSubcategoryId === '') {
            $editingSubcategoryId = null;
        }
        $isEditingContext = $editingSubcategoryId !== null;
        $createErrors = $isEditingContext ? null : ($errors->any() ? $errors : null);
    @endphp

    <div class="card card-outline card-secondary">
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
                                <td>{{ $subcategory->description ?: '—' }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-secondary" type="button"
                                            data-toggle="collapse" data-target="#{{ $collapseId }}" aria-expanded="{{ $shouldExpand ? 'true' : 'false' }}">
                                            <i class="fas fa-edit mr-1"></i>{{ __('Edit') }}
                                        </button>
                                        <form method="POST" action="{{ route('admin.report-categories.subcategories.destroy', [$category, $subcategory]) }}"
                                            onsubmit="return confirm('{{ __('Delete this subcategory? This cannot be undone.') }}');"
                                            class="d-inline-block ml-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="fas fa-trash mr-1"></i>{{ __('Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <tr class="collapse {{ $shouldExpand ? 'show' : '' }}" id="{{ $collapseId }}">
                                <td colspan="4">
                                    <div class="border rounded p-3 bg-light">
                                        <form method="POST" action="{{ route('admin.report-categories.subcategories.update', [$category, $subcategory]) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="subcategory_id" value="{{ $subcategory->id }}">
                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label for="edit-name-{{ $subcategory->id }}" class="small text-muted text-uppercase">{{ __('Name') }}</label>
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
                                                <div class="form-group col-md-4">
                                                    <label for="edit-description-{{ $subcategory->id }}" class="small text-muted text-uppercase">{{ __('Description (optional)') }}</label>
                                                    @php
                                                        $descriptionError = $rowErrors?->first('description');
                                                    @endphp
                                                    <input type="text" id="edit-description-{{ $subcategory->id }}" name="description"
                                                        class="form-control form-control-sm {{ $descriptionError ? 'is-invalid' : '' }}"
                                                        value="{{ $hasRowErrors ? old('description') : $subcategory->description }}"
                                                        maxlength="255">
                                                    @if ($descriptionError)
                                                        <div class="invalid-feedback">{{ $descriptionError }}</div>
                                                    @endif
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label for="edit-position-{{ $subcategory->id }}" class="small text-muted text-uppercase">{{ __('Order') }}</label>
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
                                                </div>
                                                <div class="form-group col-md-2 d-flex align-items-end justify-content-end">
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-save mr-1"></i>{{ __('Update') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    {{ __('No subcategories found. Use the form below to add one.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            <h5 class="mb-3">{{ __('Add subcategory') }}</h5>
            <form method="POST" action="{{ route('admin.report-categories.subcategories.store', $category) }}">
                @csrf
                <input type="hidden" name="subcategory_id" value="">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="new-name">{{ __('Name') }}</label>
                        @php
                            $createNameError = $createErrors?->first('name');
                        @endphp
                        <input type="text" id="new-name" name="name" class="form-control {{ $createNameError ? 'is-invalid' : '' }}"
                            value="{{ $createErrors ? old('name') : '' }}" maxlength="150" required>
                        @if ($createNameError)
                            <div class="invalid-feedback">{{ $createNameError }}</div>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label for="new-description">{{ __('Description (optional)') }}</label>
                        @php
                            $createDescriptionError = $createErrors?->first('description');
                        @endphp
                        <input type="text" id="new-description" name="description"
                            class="form-control {{ $createDescriptionError ? 'is-invalid' : '' }}"
                            value="{{ $createErrors ? old('description') : '' }}" maxlength="255">
                        @if ($createDescriptionError)
                            <div class="invalid-feedback">{{ $createDescriptionError }}</div>
                        @endif
                    </div>
                    <div class="form-group col-md-2">
                        <label for="new-position">{{ __('Order') }}</label>
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
                    </div>
                    <div class="form-group col-md-2 d-flex align-items-end justify-content-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus mr-1"></i>{{ __('Add') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
