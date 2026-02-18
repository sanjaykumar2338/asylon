<x-admin-layout>
    <x-slot name="header">
        {{ __('Edit Escalation Rule') }}
    </x-slot>

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title mb-0">{{ __('Edit rule') }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.escalation-rules.update', $rule) }}">
                @csrf
                @method('PUT')
                @include('admin.escalation-rules.form', ['rule' => $rule])
            </form>
        </div>
    </div>
</x-admin-layout>
