<x-admin-layout>
    <x-slot name="header">
        {{ __('Contact Message Details') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $contactMessage->first_name }} {{ $contactMessage->last_name }}</strong>
                <span class="text-muted d-block">
                    {{ __('Submitted :date', ['date' => $contactMessage->created_at?->format('M d, Y g:i A')]) }}
                </span>
            </div>
            <div class="btn-group">
                <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('Back') }}
                </a>
                <form action="{{ route('admin.contact-messages.destroy', $contactMessage) }}" method="POST"
                    data-swal-confirm
                    data-swal-title="{{ __('Delete message') }}"
                    data-swal-message="{{ __('Delete this submission?') }}"
                    data-swal-confirm-button="{{ __('Yes, delete') }}"
                    data-swal-icon="warning">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-trash mr-1"></i> {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">{{ __('Name') }}</dt>
                <dd class="col-sm-9">{{ $contactMessage->first_name }} {{ $contactMessage->last_name }}</dd>

                <dt class="col-sm-3">{{ __('Message') }}</dt>
                <dd class="col-sm-9">
                    <p class="mb-0">{!! nl2br(e($contactMessage->message)) !!}</p>
                </dd>
            </dl>
        </div>
    </div>
</x-admin-layout>
