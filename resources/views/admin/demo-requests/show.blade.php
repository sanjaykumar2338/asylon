<x-admin-layout>
    <x-slot name="header">
        {{ __('Demo Request Details') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $demoRequest->first_name }} {{ $demoRequest->last_name }}</strong>
                <span class="text-muted d-block">
                    {{ __('Submitted :date', ['date' => $demoRequest->created_at?->format('M d, Y g:i A')]) }}
                </span>
            </div>
            <div class="btn-group">
                <a href="{{ route('admin.demo-requests.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('Back') }}
                </a>
                <form action="{{ route('admin.demo-requests.destroy', $demoRequest) }}" method="POST"
                    data-swal-confirm
                    data-swal-title="{{ __('Delete demo request') }}"
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
                <dd class="col-sm-9">{{ $demoRequest->first_name }} {{ $demoRequest->last_name }}</dd>

                <dt class="col-sm-3">{{ __('Email') }}</dt>
                <dd class="col-sm-9">
                    <a href="mailto:{{ $demoRequest->email }}">{{ $demoRequest->email }}</a>
                </dd>

                <dt class="col-sm-3">{{ __('Organization') }}</dt>
                <dd class="col-sm-9">{{ $demoRequest->organization }}</dd>

                <dt class="col-sm-3">{{ __('Organization Type') }}</dt>
                <dd class="col-sm-9">{{ $demoRequest->organization_type ?? '—' }}</dd>

                <dt class="col-sm-3">{{ __('Role') }}</dt>
                <dd class="col-sm-9">{{ $demoRequest->role ?? '—' }}</dd>

                <dt class="col-sm-3">{{ __('Phone') }}</dt>
                <dd class="col-sm-9">{{ $demoRequest->phone ?? '—' }}</dd>

                <dt class="col-sm-3">{{ __('Preferred Meeting') }}</dt>
                <dd class="col-sm-9">{{ $demoRequest->meeting ?? '—' }}</dd>

                <dt class="col-sm-3">{{ __('Preferred Time Window') }}</dt>
                <dd class="col-sm-9">{{ $demoRequest->time_window ?? '—' }}</dd>

                <dt class="col-sm-3">{{ __('Top concerns') }}</dt>
                <dd class="col-sm-9">
                    @if ($demoRequest->concerns)
                        <p class="mb-0">{!! nl2br(e($demoRequest->concerns)) !!}</p>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </dd>
            </dl>
        </div>
    </div>
</x-admin-layout>
