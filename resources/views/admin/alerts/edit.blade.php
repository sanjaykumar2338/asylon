<x-admin-layout>
    <x-slot name="header">
        {{ __('Edit Alert Contact') }}
    </x-slot>

    <div class="row">
        <div class="col-lg-8">
            @include('admin.partials.flash')

            <div class="card card-outline card-primary">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.alerts.update', $alert) }}">
                        @csrf
                        @method('PUT')

                        @include('admin.alerts.form', ['alert' => $alert, 'departments' => $departments])

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('admin.alerts.index') }}" class="btn btn-outline-secondary">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary ml-2">
                                <i class="fas fa-save mr-1"></i> {{ __('Update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
