@php
    use Illuminate\Support\Str;
@endphp

<x-admin-layout>
    <x-slot name="header">
        {{ __('Data Deletion Requests') }}
    </x-slot>

    <div class="card card-outline card-primary mb-4">
        <div class="card-header">
            <h3 class="card-title mb-0">
                <i class="fas fa-filter mr-2"></i> {{ __('Filters') }}
            </h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.data_requests.index') }}">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="status">{{ __('Status') }}</label>
                        <select id="status" name="status" class="form-control">
                            <option value="">{{ __('All') }}</option>
                            @foreach (['new','in_review','completed','rejected'] as $option)
                                <option value="{{ $option }}" @selected($status === $option)>{{ Str::headline($option) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="scope">{{ __('Scope') }}</label>
                        <select id="scope" name="scope" class="form-control">
                            <option value="">{{ __('All') }}</option>
                            @foreach (['reporter_pii' => __('Reporter PII'), 'cases' => __('Cases'), 'account' => __('Account')] as $value => $label)
                                <option value="{{ $value }}" @selected($scope === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="from">{{ __('Requested from') }}</label>
                        <input type="date" id="from" name="from" class="form-control" value="{{ $from }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="to">{{ __('Requested to') }}</label>
                        <input type="date" id="to" name="to" class="form-control" value="{{ $to }}">
                    </div>
                    @if (($orgOptions ?? collect())->isNotEmpty())
                        <div class="form-group col-md-3">
                            <label for="org_id">{{ __('Organization') }}</label>
                            <select id="org_id" name="org_id" class="form-control">
                                <option value="0">{{ __('All') }}</option>
                                @foreach ($orgOptions as $org)
                                    <option value="{{ $org->id }}" @selected((string)($orgId ?? '') === (string)$org->id)>{{ $org->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
                <div class="d-flex flex-column flex-sm-row justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary mr-sm-2 mb-2 mb-sm-0">
                        <i class="fas fa-search mr-1"></i> {{ __('Apply') }}
                    </button>
                    <a href="{{ route('admin.data_requests.index') }}" class="btn btn-outline-secondary">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-clipboard-list mr-2"></i> {{ __('Requests') }}
            </h3>
            <span class="badge badge-info badge-pill px-3 py-2 mt-3 mt-md-0">
                {{ __('Results') }}: {{ number_format($requests->total()) }}
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
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
        <div class="card-footer">
            {{ $requests->links('pagination::bootstrap-4') }}
        </div>
    </div>
</x-admin-layout>
