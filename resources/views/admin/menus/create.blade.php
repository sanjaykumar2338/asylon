<x-admin-layout>
    <x-slot name="header">
        {{ __('New Menu') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Create Menu') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Define a new navigation menu and location.') }}</p>
            </div>
        </div>

        <form action="{{ route('admin.menus.store') }}" method="POST">
            @csrf
            <div class="card admin-index-card">
                <div class="card-body">
                    @include('admin.menus.partials.form', ['menu' => $menu])
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary px-3 py-2">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary ml-2 px-3 py-2">
                        <i class="fas fa-save mr-1"></i> {{ __('Save') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-admin-layout>
