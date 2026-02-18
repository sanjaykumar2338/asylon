@php
    use Illuminate\Support\Str;
@endphp

<x-admin-layout>
    <x-slot name="header">
        {{ __('Data Deletion Request') }} #{{ $requestItem->id }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="card card-outline card-primary mb-4">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-info-circle mr-2"></i> {{ __('Request Details') }}
            </h3>
            <span class="badge badge-{{ $requestItem->status === 'completed' ? 'success' : ($requestItem->status === 'rejected' ? 'danger' : 'warning') }}">
                {{ Str::headline($requestItem->status) }}
            </span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h6 class="text-muted small text-uppercase">{{ __('Requested') }}</h6>
                    <div>{{ $requestItem->requested_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <h6 class="text-muted small text-uppercase">{{ __('Due') }}</h6>
                    <div>{{ $requestItem->due_at?->format('Y-m-d') ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <h6 class="text-muted small text-uppercase">{{ __('Organization') }}</h6>
                    <div>{{ $requestItem->org?->name ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <h6 class="text-muted small text-uppercase">{{ __('Scope') }}</h6>
                    <div class="text-capitalize">{{ str_replace('_', ' ', $requestItem->scope) }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <h6 class="text-muted small text-uppercase">{{ __('Reference') }}</h6>
                    <div class="text-monospace small">{{ $requestItem->reference_value ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <h6 class="text-muted small text-uppercase">{{ __('Requester') }}</h6>
                    <div>{{ $requestItem->requester_name ?? '—' }}</div>
                    <div class="text-muted small">{{ $requestItem->requester_email ?? '—' }}</div>
                    <div class="text-muted small">{{ $requestItem->requester_phone ?? '' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <h6 class="text-muted small text-uppercase">{{ __('Processed By') }}</h6>
                    <div>{{ $requestItem->processor?->name ?? '—' }}</div>
                    <div class="text-muted small">{{ $requestItem->processed_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') ?? '' }}</div>
                </div>
                <div class="col-md-8 mb-3">
                    <h6 class="text-muted small text-uppercase">{{ __('Notes') }}</h6>
                    <div class="border rounded p-3" style="min-height: 120px;">
                        {!! nl2br(e($requestItem->notes ?? '—')) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title mb-0">
                <i class="fas fa-edit mr-2"></i> {{ __('Update Status') }}
            </h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.data_requests.update_status', $requestItem) }}">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="status">{{ __('Status') }}</label>
                        <select id="status" name="status" class="form-control">
                            @foreach (['new','in_review','completed','rejected'] as $option)
                                <option value="{{ $option }}" @selected($requestItem->status === $option)>{{ Str::headline($option) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="notes">{{ __('Notes') }}</label>
                    <textarea id="notes" name="notes" rows="4" class="form-control" placeholder="{{ __('Internal notes') }}">{{ old('notes', $requestItem->notes) }}</textarea>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> {{ __('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
