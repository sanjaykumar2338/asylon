@php
    use Illuminate\Support\Str;
    $pageTitle = __('Audit Logs');
    $ultraPrivate = config('asylon.ultra_private_mode', false);
@endphp


<x-admin-layout :title="$pageTitle">
    <x-slot name="header">
        {{ $pageTitle }}
    </x-slot>

    <div class="card card-outline card-primary mb-4">
        <div class="card-header">
            <h3 class="card-title mb-0">
                <i class="fas fa-filter mr-2"></i> {{ __('Filters') }}
            </h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.audit-logs.index') }}">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="from">{{ __('From date') }}</label>
                        <input type="date" name="from" id="from" class="form-control" value="{{ $from }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="to">{{ __('To date') }}</label>
                        <input type="date" name="to" id="to" class="form-control" value="{{ $to }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="user_id">{{ __('User') }}</label>
                        <select name="user_id" id="user_id" class="form-control">
                            <option value="">{{ __('All users') }}</option>
                            @foreach ($userOptions as $option)
                                <option value="{{ $option->id }}" @selected($userId === (string) $option->id)>
                                    {{ $option->name }} @if($option->role) ({{ $option->role }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="action">{{ __('Action') }}</label>
                        <select name="action" id="action" class="form-control">
                            <option value="">{{ __('All actions') }}</option>
                            @foreach ($actionOptions as $option)
                                <option value="{{ $option }}" @selected($action === $option)>{{ Str::headline($option) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="case_id">{{ __('Case ID') }}</label>
                        <input type="text" name="case_id" id="case_id" class="form-control" value="{{ $caseId }}" placeholder="{{ __('Report ULID') }}">
                    </div>
                </div>
                <div class="d-flex flex-column flex-sm-row justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary mr-sm-2 mb-2 mb-sm-0">
                        <i class="fas fa-search mr-1"></i> {{ __('Apply') }}
                    </button>
                    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-clipboard-list mr-2"></i> {{ __('Audit Logs') }}
            </h3>
            <span class="badge badge-info badge-pill px-3 py-2 mt-3 mt-md-0">
                {{ __('Results') }}: {{ number_format($logs->total()) }}
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">{{ __('Timestamp') }}</th>
                        <th scope="col">{{ __('User') }}</th>
                        <th scope="col">{{ __('Action') }}</th>
                        <th scope="col">{{ __('Case ID') }}</th>
                        <th scope="col">{{ __('IP address') }}</th>
                        <th scope="col">{{ __('Meta') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        @php
                            $sensitive = $ultraPrivate || ($log->org?->enable_ultra_private_mode ?? false);
                        @endphp
                        <tr>
                            <td class="text-nowrap">{{ $log->created_at?->timezone(config('app.timezone'))->format('Y-m-d H:i:s') }}</td>
                            <td>
                                @if ($log->user)
                                    <div class="font-weight-bold">{{ $log->user->name }}</div>
                                    <div class="text-muted small">{{ $log->user->role }}</div>
                                @else
                                    <span class="text-muted">{{ __('System') }}</span>
                                @endif
                            </td>
                            <td>{{ Str::headline($log->action ?? __('Unknown')) }}</td>
                            <td class="text-monospace">
                                @if ($log->case_id)
                                    @can('review-reports')
                                        <a href="{{ route('reports.show', $log->case_id) }}">{{ $log->case_id }}</a>
                                    @else
                                        {{ $log->case_id }}
                                    @endcan
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                            <td>
                                @if ($sensitive)
                                    <span class="text-muted">{{ __('Hidden') }}</span>
                                @else
                                    {{ $log->ip_address ?? __('Unknown') }}
                                @endif
                            </td>
                            <td>
                                @if (!empty($log->meta))
                                    <pre class="mb-0 small">{{ json_encode($log->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                {{ __('No audit logs found for the selected filters.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $logs->links('pagination::bootstrap-4') }}
        </div>
    </div>
</x-admin-layout>
