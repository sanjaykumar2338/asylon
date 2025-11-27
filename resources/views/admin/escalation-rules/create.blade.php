<x-admin-layout>
    <x-slot name="header">
        {{ __('New Escalation Rule') }}
    </x-slot>

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title mb-0">{{ __('Create rule') }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.escalation-rules.store') }}">
                @csrf
                @include('admin.escalation-rules.form', ['rule' => null])
            </form>
        </div>
    </div>
</x-admin-layout>
