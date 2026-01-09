@php
    use Illuminate\Support\Str;
@endphp

<x-admin-layout>
    <x-slot name="header">
        {{ __('Data Deletion Requests') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Data Deletion Requests') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Track and process incoming data deletion requests.') }}</p>
            </div>
            <span class="badge badge-info badge-pill px-3 py-2">
                {{ __('Results') }}: {{ number_format($requests->total()) }}
            </span>
        </div>

        <div class="card admin-index-card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.data_requests.index') }}" class="admin-filter-bar mb-0">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label for="status">{{ __('Status') }}</label>
                            <select id="status" name="status" class="form-control">
                                <option value="">{{ __('All') }}</option>
                                @foreach (['new','in_review','completed','rejected'] as $option)
                                    <option value="{{ $option }}" @selected($status === $option)>{{ Str::headline($option) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="scope">{{ __('Scope') }}</label>
                            <select id="scope" name="scope" class="form-control">
                                <option value="">{{ __('All') }}</option>
                                @foreach (['reporter_pii' => __('Reporter PII'), 'cases' => __('Cases'), 'account' => __('Account')] as $value => $label)
                                    <option value="{{ $value }}" @selected($scope === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="from">{{ __('Requested from') }}</label>
                            <input type="date" id="from" name="from" class="form-control" value="{{ $from }}">
                        </div>
                        <div class="col-md-3">
                            <label for="to">{{ __('Requested to') }}</label>
                            <input type="date" id="to" name="to" class="form-control" value="{{ $to }}">
                        </div>
                        @if (($orgOptions ?? collect())->isNotEmpty())
                            <div class="col-md-3">
                                <label for="org_id">{{ __('Organization') }}</label>
                                <select id="org_id" name="org_id" class="form-control">
                                    <option value="0">{{ __('All') }}</option>
                                    @foreach ($orgOptions as $org)
                                        <option value="{{ $org->id }}" @selected((string)($orgId ?? '') === (string)$org->id)>{{ $org->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-md-12 text-md-end">
                            <button type="submit" class="btn btn-outline-primary mr-1">
                                <i class="fas fa-filter mr-1"></i> {{ __('Apply') }}
                            </button>
                            <a href="{{ route('admin.data_requests.index') }}" class="btn btn-light">
                                {{ __('Reset') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card admin-index-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">{{ __('Requested') }}</th>
                                <th scope="col">{{ __('Due') }}</th>
                                <th scope="col">{{ __('Requester') }}</th>
                                <th scope="col">{{ __('Scope') }}</th>
                                <th scope="col">{{ __('Reference') }}</th>
                                <th scope="col">{{ __('Status') }}</th>
                                <th scope="col">{{ __('Org') }}</th>
                                <th scope="col" class="text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($requests as $item)
                                <tr>
                                    <td class="text-nowrap">{{ $item->requested_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</td>
                                    <td class="text-nowrap">
                                        {{ $item->due_at?->format('Y-m-d') }}
                                        @if ($item->due_at)
                                            <div class="small text-muted">
                                                {{ now()->diffInDays($item->due_at, false) }} {{ __('days remaining') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $item->requester_email ?? '—' }}</div>
                                        <div class="small text-muted">{{ $item->requester_name ?? '' }}</div>
                                    </td>
                                    <td class="text-capitalize">{{ str_replace('_', ' ', $item->scope) }}</td>
                                    <td class="text-monospace small">{{ $item->reference_value ?? '—' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $item->status === 'completed' ? 'success' : ($item->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ Str::headline($item->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $item->org?->name ?? '—' }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.data_requests.show', $item) }}" class="btn btn-outline-primary btn-sm">
                                            {{ __('View') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        {{ __('No data deletion requests found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($requests->hasPages())
                <div class="pt-3 px-3">
                    {{ $requests->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
