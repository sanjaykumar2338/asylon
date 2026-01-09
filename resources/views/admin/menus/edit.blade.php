<x-admin-layout>
    <x-slot name="header">
        {{ __('Edit Menu') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Edit Menu') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Update menu details, add items, and manage ordering.') }}</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-4">
                <div class="card admin-index-card mb-3">
                    <div class="card-body">
                        <h2 class="h6 mb-3 text-uppercase text-muted">{{ __('Menu') }}</h2>
                        <form action="{{ route('admin.menus.update', $menu) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @include('admin.menus.partials.form', ['menu' => $menu])
                            <div class="d-flex justify-content-end mt-3">
                                <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary px-3 py-2">
                                    {{ __('Cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary ml-2 px-3 py-2">
                                    <i class="fas fa-save mr-1"></i> {{ __('Save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card admin-index-card">
                    <div class="card-body">
                        <h2 class="h6 mb-3 text-uppercase text-muted">{{ __('Add Menu Item') }}</h2>
                        <form action="{{ route('admin.menus.items.store', $menu) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="item-title">{{ __('Title') }}</label>
                                <input type="text" name="title" id="item-title" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="item-type">{{ __('Type') }}</label>
                                <select name="type" id="item-type" class="form-control">
                                    <option value="page">{{ __('Page') }}</option>
                                    <option value="url">{{ __('Custom URL') }}</option>
                                </select>
                            </div>
                            <div class="form-group" id="page-select-group">
                                <label for="item-page">{{ __('Page') }}</label>
                                <select name="page_id" id="item-page" class="form-control">
                                    @foreach($pages as $page)
                                        <option value="{{ $page->id }}">{{ $page->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="url-input-group" style="display: none;">
                                <label for="item-url">{{ __('URL') }}</label>
                                <input type="text" name="url" id="item-url" class="form-control" placeholder="https://example.com/page">
                            </div>
                            <div class="form-group">
                                <label for="item-target">{{ __('Target') }}</label>
                                <select name="target" id="item-target" class="form-control">
                                    <option value="_self">{{ __('Same tab') }}</option>
                                    <option value="_blank">{{ __('New tab') }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="item-parent">{{ __('Parent (optional)') }}</label>
                                <select name="parent_id" id="item-parent" class="form-control">
                                    <option value="">{{ __('None') }}</option>
                                    @foreach($menu->items as $item)
                                        <option value="{{ $item->id }}">{{ $item->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="btn btn-primary px-3 py-2">
                                    <i class="fas fa-plus mr-1"></i> {{ __('Add Item') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card admin-index-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">{{ __('Menu Items') }}</h3>
                        <small class="text-muted">{{ __('Drag to reorder') }}</small>
                    </div>
                    <div class="card-body p-0">
                        <form action="{{ route('admin.menus.items.reorder', $menu) }}" method="POST" id="reorder-form">
                            @csrf
                            <input type="hidden" name="order[]" id="order-field">
                        </form>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;"></th>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Link') }}</th>
                                        <th>{{ __('Target') }}</th>
                                        <th style="width: 150px;" class="text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="menu-items-sortable">
                                    @forelse($menu->items as $item)
                                        <tr data-id="{{ $item->id }}">
                                            <td class="align-middle"><i class="fas fa-grip-vertical text-muted menu-sort-handle"></i></td>
                                            <td class="align-middle">
                                                {{ $item->title }}
                                                @if($item->parent_id)
                                                    <small class="text-muted d-block">{{ __('Child of') }} #{{ $item->parent_id }}</small>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if($item->type === 'page' && $item->page)
                                                    <span class="badge badge-info">{{ __('Page') }}</span>
                                                    <a href="{{ route('pages.show', $item->page->slug) }}" target="_blank">{{ $item->page->title }}</a>
                                                @else
                                                    <span class="badge badge-secondary">{{ __('URL') }}</span>
                                                    <a href="{{ $item->url }}" target="_blank">{{ $item->url }}</a>
                                                @endif
                                            </td>
                                            <td class="align-middle">{{ $item->target }}</td>
                                            <td class="align-middle text-right">
                                                <button class="btn btn-sm btn-outline-primary" type="button"
                                                    data-toggle="collapse" data-target="#edit-item-{{ $item->id }}"
                                                    data-bs-toggle="collapse" data-bs-target="#edit-item-{{ $item->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('admin.menus.items.destroy', [$menu, $item]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Delete this item?') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <tr class="collapse bg-light" id="edit-item-{{ $item->id }}">
                                            <td colspan="5">
                                                <div class="p-3">
                                                    <form action="{{ route('admin.menus.items.update', [$menu, $item]) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row g-3">
                                                            <div class="col-md-4">
                                                                <div class="form-group mb-2">
                                                                    <label class="small text-muted">{{ __('Title') }}</label>
                                                                    <input type="text" name="title" class="form-control" value="{{ $item->title }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group mb-2">
                                                                    <label class="small text-muted">{{ __('Type') }}</label>
                                                                    <select name="type" class="form-control item-type-select">
                                                                        <option value="page" {{ $item->type === 'page' ? 'selected' : '' }}>{{ __('Page') }}</option>
                                                                        <option value="url" {{ $item->type === 'url' ? 'selected' : '' }}>{{ __('Custom URL') }}</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5 item-page-group" style="{{ $item->type === 'page' ? '' : 'display:none;' }}">
                                                                <div class="form-group mb-2">
                                                                    <label class="small text-muted">{{ __('Page') }}</label>
                                                                    <select name="page_id" class="form-control">
                                                                        @foreach($pages as $page)
                                                                            <option value="{{ $page->id }}" {{ $item->page_id === $page->id ? 'selected' : '' }}>{{ $page->title }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5 item-url-group" style="{{ $item->type === 'url' ? '' : 'display:none;' }}">
                                                                <div class="form-group mb-2">
                                                                    <label class="small text-muted">{{ __('URL') }}</label>
                                                                    <input type="text" name="url" class="form-control" value="{{ $item->url }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group mb-2">
                                                                    <label class="small text-muted">{{ __('Target') }}</label>
                                                                    <select name="target" class="form-control">
                                                                        <option value="_self" {{ $item->target === '_self' ? 'selected' : '' }}>{{ __('Same tab') }}</option>
                                                                        <option value="_blank" {{ $item->target === '_blank' ? 'selected' : '' }}>{{ __('New tab') }}</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group mb-2">
                                                                    <label class="small text-muted">{{ __('Parent') }}</label>
                                                                    <select name="parent_id" class="form-control">
                                                                        <option value="">{{ __('None') }}</option>
                                                                        @foreach($menu->items as $potentialParent)
                                                                            <option value="{{ $potentialParent->id }}" {{ $item->parent_id === $potentialParent->id ? 'selected' : '' }}>
                                                                                {{ $potentialParent->title }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex justify-content-end mt-3">
                                                            <button type="submit" class="btn btn-primary btn-sm px-3 py-2">
                                                                <i class="fas fa-save mr-1"></i> {{ __('Update Item') }}
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted p-4">{{ __('No menu items yet.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($menu->items->count())
                        <div class="card-footer text-right">
                            <button form="reorder-form" class="btn btn-primary btn-sm px-3 py-2">
                                <i class="fas fa-save mr-1"></i> {{ __('Save Order') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeSelect = document.getElementById('item-type');
            const pageGroup = document.getElementById('page-select-group');
            const urlGroup = document.getElementById('url-input-group');

            function toggleAddInputs() {
                if (!typeSelect || !pageGroup || !urlGroup) {
                    return;
                }
                if (typeSelect.value === 'page') {
                    pageGroup.style.display = '';
                    urlGroup.style.display = 'none';
                } else {
                    pageGroup.style.display = 'none';
                    urlGroup.style.display = '';
                }
            }
            if (typeSelect) {
                typeSelect.addEventListener('change', toggleAddInputs);
                toggleAddInputs();
            }

            document.querySelectorAll('.item-type-select').forEach(function (selectEl) {
                selectEl.addEventListener('change', function () {
                    const row = selectEl.closest('form');
                    const pageGroup = row.querySelector('.item-page-group');
                    const urlGroup = row.querySelector('.item-url-group');
                    if (selectEl.value === 'page') {
                        pageGroup.style.display = '';
                        urlGroup.style.display = 'none';
                    } else {
                        pageGroup.style.display = 'none';
                        urlGroup.style.display = '';
                    }
                });
            });

            function initSortable() {
                const sortableEl = document.getElementById('menu-items-sortable');
                const orderField = document.getElementById('order-field');
                if (!sortableEl || !orderField || !window.Sortable) {
                    return;
                }

                new Sortable(sortableEl, {
                    handle: '.menu-sort-handle',
                    animation: 150,
                    draggable: 'tr[data-id]',
                    forceFallback: true,
                    fallbackOnBody: true,
                    fallbackTolerance: 3,
                    onSort: function () {
                        const ids = Array.from(sortableEl.querySelectorAll('tr[data-id]')).map(row => row.dataset.id);
                        orderField.value = ids.join(',');
                    }
                });
                const initialIds = Array.from(sortableEl.querySelectorAll('tr[data-id]')).map(row => row.dataset.id);
                orderField.value = initialIds.join(',');

                const reorderForm = document.getElementById('reorder-form');
                if (reorderForm) {
                    reorderForm.addEventListener('submit', function () {
                        const container = reorderForm;
                        const current = orderField.value ? orderField.value.split(',') : [];
                        orderField.remove();
                        current.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'order[]';
                            input.value = id;
                            container.appendChild(input);
                        });
                    });
                }
            }

            (function loadSortable() {
                if (window.Sortable) {
                    initSortable();
                    return;
                }
                const localScript = document.createElement('script');
                localScript.src = "{{ asset('vendor/sortable.min.js') }}";
                localScript.onload = function () {
                    if (window.Sortable) {
                        initSortable();
                    } else {
                        const cdn = document.createElement('script');
                        cdn.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js';
                        cdn.onload = initSortable;
                        document.head.appendChild(cdn);
                    }
                };
                localScript.onerror = function () {
                    const cdn = document.createElement('script');
                    cdn.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js';
                    cdn.onload = initSortable;
                    document.head.appendChild(cdn);
                };
                document.head.appendChild(localScript);
            })();
        });
    </script>
@endpush
