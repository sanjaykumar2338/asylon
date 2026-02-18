<x-admin-layout>
    <x-slot name="header">
        {{ __('Invite User') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Invite User') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Send an invitation to join the platform and set their role.') }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card admin-index-card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.users.store') }}">
                            @csrf

                            @include('admin.users.form')

                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-3 py-2">
                                    {{ __('Cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary ml-2 px-3 py-2">
                                    <i class="fas fa-paper-plane mr-1"></i> {{ __('Invite') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
