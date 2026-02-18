<x-admin-layout>
    <x-slot name="header">
        {{ __('Add Alert Contact') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Add Alert Contact') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Create a new alert contact for notifications by type and organization.') }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card admin-index-card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.alerts.store') }}">
                            @csrf

                            @include('admin.alerts.form', ['departments' => $departments])

                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('admin.alerts.index') }}" class="btn btn-outline-secondary px-3 py-2">
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
