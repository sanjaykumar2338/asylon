@php
        $cleanString = static function ($value): string {
            return is_string($value) ? $value : (is_numeric($value) ? (string) $value : '');
        };

        $portalValue = $cleanString($filters['portal'] ?? '');
        $fromValue = $cleanString($filters['from'] ?? '');
        $toValue = $cleanString($filters['to'] ?? '');
        $orgIdValue = is_numeric($filters['org_id'] ?? null) ? (int) $filters['org_id'] : '';
        $rangeValue = isset($filters['range']) && is_numeric($filters['range']) ? (int) $filters['range'] : 30;
        $displayValue = static function ($value): string {
            if (is_array($value)) {
                $flat = array_filter($value, static fn ($v) => is_scalar($v));
                return implode(', ', array_map('strval', $flat));
            }

            return is_scalar($value) ? (string) $value : '';
        };
        $translate = static function (string $text) use ($displayValue): string {
            $translated = __($text);
            $string = $displayValue($translated);
            return $string !== '' ? $string : $text;
        };
@endphp

<x-admin-layout>
    <x-slot name="header">
        {{ $translate('Analytics') }}
    </x-slot>

    <div class="alert alert-info mb-4">
        {{ $translate('These analytics help your team see where risk is concentrated and how reports are trending over time.') }}
    </div>

    <div class="card card-outline card-primary mb-4">
        <div class="card-body">
            <form method="GET" class="form-row">
                <div class="form-group col-md-3">
                    <label class="small text-uppercase text-muted">{{ $translate('Portal') }}</label>
                    <select name="portal" class="form-control">
                        <option value="">{{ $translate('All portals') }}</option>
                        <option value="student" @selected($portalValue === 'student')>{{ $translate('Student only') }}</option>
                        <option value="employee" @selected($portalValue === 'employee')>{{ $translate('HR/Employee only') }}</option>
                        <option value="general" @selected($portalValue === 'general')>{{ $translate('General') }}</option>
                    </select>
                </div>
                @if (isset($orgOptions) && $orgOptions->count() > 1)
                    <div class="form-group col-md-3">
                        <label class="small text-uppercase text-muted">{{ $translate('Organization') }}</label>
                        <select name="org_id" class="form-control">
                            <option value="">{{ $translate('All visible') }}</option>
                            @foreach ($orgOptions as $org)
                                <option value="{{ (string) $org->id }}" @selected($orgIdValue === $org->id)>{{ $displayValue($org->name) ?: __('(Unnamed organization)') }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="form-group col-md-3">
                    <label class="small text-uppercase text-muted">{{ $translate('From date') }}</label>
                    <input type="date" name="from" value="{{ $fromValue }}" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label class="small text-uppercase text-muted">{{ $translate('To date') }}</label>
                    <input type="date" name="to" value="{{ $toValue }}" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label class="small text-uppercase text-muted">{{ $translate('Range (days)') }}</label>
                    <select name="range" class="form-control">
                        @foreach ([7, 14, 30, 60, 90] as $rangeOption)
                            <option value="{{ $rangeOption }}" @selected($rangeValue === $rangeOption)>{{ $rangeOption }} {{ $translate('days') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-12 text-right mb-0">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter mr-1"></i> {{ $translate('Apply filters') }}
                    </button>
                    <a href="{{ route('admin.analytics') }}" class="btn btn-outline-secondary">
                        {{ $translate('Reset') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    @php
        $totals = $metrics['totals'] ?? [];
        $timeseries = $metrics['timeseries'] ?? [];
        $byCategory = collect($metrics['by_category'] ?? []);
    @endphp

    <div class="row">
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <p class="text-muted text-uppercase mb-2">{{ $translate('Total reports') }}</p>
                    <h3 class="font-weight-bold mb-0">{{ number_format($totals['total_reports'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <p class="text-muted text-uppercase mb-2">{{ $translate('High / Critical risk') }}</p>
                    <h3 class="font-weight-bold mb-1">{{ number_format($totals['high_risk_reports'] ?? 0) }}</h3>
                    <span class="badge badge-danger">{{ $translate('High attention') }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <p class="text-muted text-uppercase mb-2">{{ $translate('Low / Medium risk') }}</p>
                    <h3 class="font-weight-bold mb-1">{{ number_format($totals['low_medium_risk_reports'] ?? 0) }}</h3>
                    <span class="text-muted">{{ $translate('Scored reports in lower bands') }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <p class="text-muted text-uppercase mb-2">{{ $translate('Unscored reports') }}</p>
                    <h3 class="font-weight-bold mb-1">{{ number_format($totals['no_risk_scored_reports'] ?? 0) }}</h3>
                    <span class="text-muted">{{ $translate('Reports without risk analysis yet') }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <p class="text-muted text-uppercase mb-2">{{ $translate('Open urgent reports') }}</p>
                    <h3 class="font-weight-bold mb-1">{{ number_format($totals['urgent_reports_open'] ?? 0) }}</h3>
                    <span class="text-muted">{{ $translate('Marked urgent and still open') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card card-outline card-primary h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-chart-line mr-2"></i> {{ $translate('30-day trend (Total vs High/Critical)') }}
                    </h3>
                </div>
                <div class="card-body">
                    @if (empty($timeseries))
                        <p class="text-muted mb-0">{{ $translate('No submissions yet.') }}</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ $translate('Date') }}</th>
                                        <th class="text-right">{{ $translate('Total') }}</th>
                                        <th class="text-right text-danger">{{ $translate('High/Critical') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($timeseries as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td class="text-right">{{ $row['total_reports'] }}</td>
                                            <td class="text-right text-danger">{{ $row['high_risk_reports'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card card-outline card-primary h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-tags mr-2"></i> {{ $translate('Category vs High/Critical risk') }}
                    </h3>
                </div>
                <div class="card-body">
                    @if ($byCategory->isEmpty())
                        <p class="text-muted mb-0">{{ $translate('No submissions yet.') }}</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ $translate('Category') }}</th>
                                        <th class="text-right">{{ $translate('Total') }}</th>
                                        <th class="text-right">{{ $translate('High/Critical') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($byCategory as $category)
                                        <tr>
                                            <td>{{ $displayValue($category->category) ?: __('Unknown') }}</td>
                                            <td class="text-right">{{ $category->total_reports }}</td>
                                            <td class="text-right">
                                                <span class="badge badge-danger">{{ $category->high_risk_count }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
