<x-admin-layout>
    <x-slot name="header">
        {{ __('New Escalation Rule') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Create Escalation Rule') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Configure automatic escalation based on risk level, urgency, and categories.') }}</p>
            </div>
        </div>

        <div class="card admin-index-card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.escalation-rules.store') }}">
                    @csrf
                    @include('admin.escalation-rules.form', ['rule' => null])
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
