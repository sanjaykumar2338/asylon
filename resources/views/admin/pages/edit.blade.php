<x-admin-layout>
    <x-slot name="header">
        {{ __('Edit Page') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Edit Page') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Update the page title, slug, and content.') }}</p>
            </div>
            <a href="{{ route('pages.show', $page->slug) }}" target="_blank" class="btn btn-outline-secondary px-3 py-2">
                <i class="fas fa-external-link-alt mr-1"></i> {{ __('View') }}
            </a>
        </div>

        <form action="{{ route('admin.pages.update', $page) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card admin-index-card">
                <div class="card-body">
                    @include('admin.pages._form', ['page' => $page])
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary px-3 py-2">
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
