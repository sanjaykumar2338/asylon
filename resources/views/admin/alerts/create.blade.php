<x-admin-layout>
    <x-slot name="header">
        {{ __('Add Alert Contact') }}
    </x-slot>

    <div class="row">
        <div class="col-lg-8">
            @include('admin.partials.flash')

            <div class="card card-outline card-primary">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.alerts.store') }}">
                        @csrf

                        @include('admin.alerts.form')

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('admin.alerts.index') }}" class="btn btn-outline-secondary">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary ml-2">
                                <i class="fas fa-save mr-1"></i> {{ __('Save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

