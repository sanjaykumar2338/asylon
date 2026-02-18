<x-admin-layout>
    <x-slot name="header">
        {{ __('Create Organization') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Create Organization') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Set up a new organization with locale, report types, and privacy options.') }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card admin-index-card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.orgs.store') }}">
                            @csrf

                            @include('admin.orgs.form')

                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('admin.orgs.index') }}" class="btn btn-outline-secondary px-3 py-2">
                                    {{ __('Cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary ml-2 px-3 py-2">
                                    <i class="fas fa-save mr-1"></i> {{ __('Save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
