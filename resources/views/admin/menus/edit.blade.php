@extends('layouts.admin', ['headerTitle' => 'Edit Menu'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4">
                <form action="{{ route('admin.menus.update', $menu) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Menu</h3>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save mr-1"></i> Save
                            </button>
                        </div>
                        <div class="card-body">
                            @include('admin.menus.partials.form', ['menu' => $menu])
                        </div>
                    </div>
                </form>

                <form action="{{ route('admin.menus.items.store', $menu) }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Add Menu Item</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="item-title">Title</label>
                                <input type="text" name="title" id="item-title" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="item-type">Type</label>
                                <select name="type" id="item-type" class="form-control">
                                    <option value="page">Page</option>
                                    <option value="url">Custom URL</option>
                                </select>
                            </div>
                            <div class="form-group" id="page-select-group">
                                <label for="item-page">Page</label>
                                <select name="page_id" id="item-page" class="form-control">
                                    @foreach($pages as $page)
                                        <option value="{{ $page->id }}">{{ $page->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="url-input-group" style="display: none;">
                                <label for="item-url">URL</label>
                                <input type="text" name="url" id="item-url" class="form-control" placeholder="https://example.com/page">
                            </div>
                            <div class="form-group">
                                <label for="item-target">Target</label>
                                <select name="target" id="item-target" class="form-control">
                                    <option value="_self">Same tab</option>
                                    <option value="_blank">New tab</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="item-parent">Parent (optional)</label>
                                <select name="parent_id" id="item-parent" class="form-control">
                                    <option value="">None</option>
                                    @foreach($menu->items as $item)
                                        <option value="{{ $item->id }}">{{ $item->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-plus mr-1"></i> Add Item
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Menu Items</h3>
                        <small class="text-muted">Drag to reorder</small>
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
                                        <th>Title</th>
                                        <th>Link</th>
                                        <th>Target</th>
                                        <th style="width: 150px;" class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="menu-items-sortable">
                                    @forelse($menu->items as $item)
                                        <tr data-id="{{ $item->id }}">
                                            <td class="align-middle"><i class="fas fa-grip-vertical text-muted"></i></td>
                                            <td class="align-middle">
                                                {{ $item->title }}
                                                @if($item->parent_id)
                                                    <small class="text-muted d-block">Child of #{{ $item->parent_id }}</small>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if($item->type === 'page' && $item->page)
                                                    <span class="badge badge-info">Page</span>
                                                    <a href="{{ route('pages.show', $item->page->slug) }}" target="_blank">{{ $item->page->title }}</a>
                                                @else
                                                    <span class="badge badge-secondary">URL</span>
                                                    <a href="{{ $item->url }}" target="_blank">{{ $item->url }}</a>
                                                @endif
                                            </td>
                                            <td class="align-middle">{{ $item->target }}</td>
                                            <td class="align-middle text-right">
                                                <button class="btn btn-sm btn-outline-primary" type="button" data-toggle="collapse" data-target="#edit-item-{{ $item->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('admin.menus.items.destroy', [$menu, $item]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this item?');">
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
                                                <form action="{{ route('admin.menus.items.update', [$menu, $item]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="form-row">
                                                        <div class="form-group col-md-4">
                                                            <label>Title</label>
                                                            <input type="text" name="title" class="form-control" value="{{ $item->title }}" required>
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <label>Type</label>
                                                            <select name="type" class="form-control item-type-select">
                                                                <option value="page" {{ $item->type === 'page' ? 'selected' : '' }}>Page</option>
                                                                <option value="url" {{ $item->type === 'url' ? 'selected' : '' }}>Custom URL</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-5 item-page-group" style="{{ $item->type === 'page' ? '' : 'display:none;' }}">
                                                            <label>Page</label>
                                                            <select name="page_id" class="form-control">
                                                                @foreach($pages as $page)
                                                                    <option value="{{ $page->id }}" {{ $item->page_id === $page->id ? 'selected' : '' }}>{{ $page->title }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-5 item-url-group" style="{{ $item->type === 'url' ? '' : 'display:none;' }}">
                                                            <label>URL</label>
                                                            <input type="text" name="url" class="form-control" value="{{ $item->url }}">
                                                        </div>
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="form-group col-md-4">
                                                            <label>Target</label>
                                                            <select name="target" class="form-control">
                                                                <option value="_self" {{ $item->target === '_self' ? 'selected' : '' }}>Same tab</option>
                                                                <option value="_blank" {{ $item->target === '_blank' ? 'selected' : '' }}>New tab</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label>Parent</label>
                                                            <select name="parent_id" class="form-control">
                                                                <option value="">None</option>
                                                                @foreach($menu->items as $potentialParent)
                                                                    <option value="{{ $potentialParent->id }}" {{ $item->parent_id === $potentialParent->id ? 'selected' : '' }}>
                                                                        {{ $potentialParent->title }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-save mr-1"></i> Update Item
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted p-4">No menu items yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($menu->items->count())
                        <div class="card-footer text-right">
                            <button form="reorder-form" class="btn btn-primary btn-sm">
                                <i class="fas fa-save mr-1"></i> Save Order
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeSelect = document.getElementById('item-type');
            const pageGroup = document.getElementById('page-select-group');
            const urlGroup = document.getElementById('url-input-group');

            function toggleAddInputs() {
                if (typeSelect.value === 'page') {
                    pageGroup.style.display = '';
                    urlGroup.style.display = 'none';
                } else {
                    pageGroup.style.display = 'none';
                    urlGroup.style.display = '';
                }
            }
            typeSelect.addEventListener('change', toggleAddInputs);
            toggleAddInputs();

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

            const sortableEl = document.getElementById('menu-items-sortable');
            const orderField = document.getElementById('order-field');
            if (sortableEl) {
                new Sortable(sortableEl, {
                    handle: '.fa-grip-vertical',
                    animation: 150,
                    draggable: 'tr[data-id]',
                    onSort: function () {
                        const ids = Array.from(sortableEl.querySelectorAll('tr[data-id]')).map(row => row.dataset.id);
                        orderField.value = ids.join(',');
                    }
                });
                // Seed initial order
                const initialIds = Array.from(sortableEl.querySelectorAll('tr[data-id]')).map(row => row.dataset.id);
                orderField.value = initialIds.join(',');
            }

            // When submitting reorder form, split comma string into multiple inputs
            const reorderForm = document.getElementById('reorder-form');
            if (reorderForm) {
                reorderForm.addEventListener('submit', function () {
                    const container = reorderForm;
                    const current = orderField.value ? orderField.value.split(',') : [];
                    // Remove placeholder single input
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
        });
    </script>
@endpush
