@php
    $canManageCategories = $canManageCategories ?? (auth()->user()?->can('manage-categories') ?? false);
@endphp

@push('styles')
    <style>
        .category-row--hidden {
            opacity: 0.65;
        }
    </style>
@endpush

<x-admin-layout>
    <x-slot name="header">
        {{ __('Report Categories') }}
    </x-slot>

    @section('breadcrumb')
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('dashboard.breadcrumb') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Report Categories') }}</li>
        </ol>
    @endsection

    <div class="card card-outline card-primary">
        <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between">
            <h3 class="card-title mb-0">
                <i class="fas fa-layer-group mr-2"></i>{{ __('Categories') }}
            </h3>
            @if ($canManageCategories)
                <a href="{{ route('admin.report-categories.create') }}" class="btn btn-primary btn-sm mt-3 mt-md-0">
                    <i class="fas fa-plus mr-1"></i>{{ __('Add Category') }}
                </a>
            @endif
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
                            <th scope="col" class="text-center" style="width: 320px;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr class="category-row {{ $category->is_hidden ? 'category-row--hidden' : '' }}"
                                data-category-row="{{ $category->id }}">
                                <td>{{ $category->position }}</td>
                                <td class="font-weight-semibold">
                                    {{ $category->name }}
                                    <span class="badge badge-secondary ml-2 {{ $category->is_hidden ? '' : 'd-none' }}"
                                        data-hidden-indicator>{{ __('Hidden') }}</span>
                                </td>
                                <td>{{ $category->description ?: __('Not provided') }}</td>
                                <td class="text-center">
                                    <span class="badge badge-info badge-pill">{{ $category->subcategories_count }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column flex-xl-row align-items-stretch justify-content-center"
                                        style="gap: 0.5rem;">
                                        <a href="{{ route('admin.report-categories.show', $category) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-sitemap mr-1"></i>{{ __('Manage Subcategories') }}
                                        </a>
                                        @if ($canManageCategories)
                                            <a href="{{ route('admin.report-categories.edit', $category) }}" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-edit mr-1"></i>{{ __('Edit') }}
                                            </a>
                                            <form method="POST" action="{{ route('admin.report-categories.destroy', $category) }}"
                                                class="d-inline-block"
                                                data-swal-confirm
                                                data-swal-title="{{ __('Delete category') }}"
                                                data-swal-message="{{ __('Are you sure you want to delete this category? This cannot be undone.') }}"
                                                data-swal-confirm-button="{{ __('Yes, delete') }}"
                                                data-swal-icon="warning">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-trash mr-1"></i>{{ __('Delete') }}
                                                </button>
                                            </form>
                                            <form method="POST"
                                                action="{{ route('admin.report-categories.toggle-visibility', $category) }}"
                                                class="toggle-visibility-form d-inline-flex"
                                                data-category-id="{{ $category->id }}"
                                                data-hidden="{{ $category->is_hidden ? '1' : '0' }}">
                                                @csrf
                                                <input type="hidden" name="category_id" value="{{ $category->id }}">
                                                <input type="hidden" name="hidden" value="{{ $category->is_hidden ? 1 : 0 }}">
                                                <button type="submit"
                                                    class="btn btn-outline-dark btn-sm"
                                                    data-hide-label="{{ __('Hide') }}"
                                                    data-unhide-label="{{ __('Unhide') }}">
                                                    @if ($category->is_hidden)
                                                        <i class="fas fa-eye mr-1"></i>{{ __('Unhide') }}
                                                    @else
                                                        <i class="fas fa-eye-slash mr-1"></i>{{ __('Hide') }}
                                                    @endif
                                                </button>
                                            </form>
                                        @endif
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

@if ($canManageCategories)
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                document.querySelectorAll('.toggle-visibility-form').forEach((form) => {
                    const button = form.querySelector('button[data-hide-label]');

                    form.addEventListener('submit', function (event) {
                        event.preventDefault();
                        if (!csrf || form.dataset.loading === '1') {
                            return;
                        }

                        form.dataset.loading = '1';
                        button.classList.add('disabled');
                        button.disabled = true;

                        const formData = new FormData(form);

                        fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            credentials: 'same-origin',
                            body: new URLSearchParams(formData),
                        })
                            .then(async (response) => {
                                const data = await response.json().catch(() => ({}));
                                if (!response.ok || !data.ok) {
                                    throw new Error(data.message || '{{ __('Unable to update category visibility.') }}');
                                }

                                const hidden = data.hidden ? '1' : '0';
                                form.dataset.hidden = hidden;
                                form.querySelector('input[name="hidden"]').value = hidden;
                                const hideLabel = button.dataset.hideLabel;
                                const unhideLabel = button.dataset.unhideLabel;
                                const row = document.querySelector(`[data-category-row=\"${form.dataset.categoryId}\"]`);
                                if (row) {
                                    row.classList.toggle('category-row--hidden', hidden === '1');
                                    const indicator = row.querySelector('[data-hidden-indicator]');
                                    if (indicator) {
                                        indicator.classList.toggle('d-none', hidden !== '1');
                                    }
                                }

                                button.innerHTML = hidden === '1'
                                    ? `<i class="fas fa-eye mr-1"></i> ${unhideLabel}`
                                    : `<i class="fas fa-eye-slash mr-1"></i> ${hideLabel}`;

                                if (window.Swal) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: data.message || (hidden === '1'
                                            ? '{{ __('Category hidden. It will no longer show up on report forms.') }}'
                                            : '{{ __('Category is visible again.') }}'),
                                        timer: 2500,
                                    });
                                } else {
                                    alert(data.message || (hidden === '1'
                                        ? '{{ __('Category hidden. It will no longer show up on report forms.') }}'
                                        : '{{ __('Category is visible again.') }}'));
                                }
                            })
                            .catch((error) => {
                                if (window.Swal) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: '{{ __('Unable to update category visibility.') }}',
                                        text: error.message,
                                    });
                                } else {
                                    alert(error.message);
                                }
                            })
                            .finally(() => {
                                form.dataset.loading = '0';
                                button.classList.remove('disabled');
                                button.disabled = false;
                            });
                    });
                });
            });
        </script>
    @endpush
@endif
