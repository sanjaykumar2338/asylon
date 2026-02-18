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
        $riskDistribution = $metrics['risk_distribution'] ?? [];
        $highRiskSummary = $metrics['high_risk_summary'] ?? [];
        $categoryHeatmap = collect($metrics['category_heatmap'] ?? []);
        $urgentInsights = $metrics['urgent_insights'] ?? [];
        $reviewerStats = collect($metrics['reviewer_stats'] ?? []);
        $distributionTotal = array_sum($riskDistribution);
        $heatBadge = static function (int $count): string {
            if ($count >= 15) {
                return 'badge-danger';
            }
            if ($count >= 7) {
                return 'badge-warning text-dark';
            }
            if ($count >= 1) {
                return 'badge-info';
            }

            return 'badge-light text-muted';
        };
@endphp

<x-admin-layout>
    <x-slot name="header">
        {{ $translate('Analytics') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ $translate('Analytics') }}</h1>
                <p class="text-muted mb-0 small">{{ $translate('Risk insights, trends, and reviewer performance.') }}</p>
            </div>
            <span class="badge badge-info badge-pill px-3 py-2">
                {{ $translate('Range') }}: {{ $rangeValue }} {{ $translate('days') }}
            </span>
        </div>

        <div class="alert alert-info mb-3">
            {{ $translate('These analytics help your team see where risk is concentrated and how reports are trending over time.') }}
        </div>

        <div class="card admin-index-card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end admin-filter-bar mb-0">
                    <div class="col-md-3">
                        <label class="small text-uppercase text-muted">{{ $translate('Portal') }}</label>
                        <select name="portal" class="form-control">
                            <option value="">{{ $translate('All portals') }}</option>
                            <option value="student" @selected($portalValue === 'student')>{{ $translate('Student only') }}</option>
                            <option value="employee" @selected($portalValue === 'employee')>{{ $translate('HR/Employee only') }}</option>
                            <option value="general" @selected($portalValue === 'general')>{{ $translate('General') }}</option>
                        </select>
                    </div>
                    @if (isset($orgOptions) && $orgOptions->count() > 1)
                        <div class="col-md-3">
                            <label class="small text-uppercase text-muted">{{ $translate('Organization') }}</label>
                            <select name="org_id" class="form-control">
                                <option value="">{{ $translate('All visible') }}</option>
                                @foreach ($orgOptions as $org)
                                    <option value="{{ (string) $org->id }}" @selected($orgIdValue === $org->id)>{{ $displayValue($org->name) ?: __('(Unnamed organization)') }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <label class="small text-uppercase text-muted">{{ $translate('From date') }}</label>
                        <input type="date" name="from" value="{{ $fromValue }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="small text-uppercase text-muted">{{ $translate('To date') }}</label>
                        <input type="date" name="to" value="{{ $toValue }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="small text-uppercase text-muted">{{ $translate('Range (days)') }}</label>
                        <select name="range" class="form-control">
                            @foreach ([7, 14, 30, 60, 90] as $rangeOption)
                                <option value="{{ $rangeOption }}" @selected($rangeValue === $rangeOption)>{{ $rangeOption }} {{ $translate('days') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 text-md-end">
                        <button type="submit" class="btn btn-outline-primary mr-1">
                            <i class="fas fa-filter mr-1"></i> {{ $translate('Apply filters') }}
                        </button>
                        <a href="{{ route('admin.analytics') }}" class="btn btn-light">
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

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <p class="text-muted text-uppercase mb-2">{{ $translate('Total reports') }}</p>
                    <h3 class="font-weight-bold mb-0">{{ number_format($totals['total_reports'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <p class="text-muted text-uppercase mb-2">{{ $translate('High / Critical risk') }}</p>
                    <h3 class="font-weight-bold mb-1">{{ number_format($totals['high_risk_reports'] ?? 0) }}</h3>
                    <span class="badge badge-danger">{{ $translate('High attention') }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <p class="text-muted text-uppercase mb-2">{{ $translate('Low / Medium risk') }}</p>
                    <h3 class="font-weight-bold mb-1">{{ number_format($totals['low_medium_risk_reports'] ?? 0) }}</h3>
                    <span class="text-muted">{{ $translate('Scored reports in lower bands') }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <p class="text-muted text-uppercase mb-2">{{ $translate('Unscored reports') }}</p>
                    <h3 class="font-weight-bold mb-1">{{ number_format($totals['no_risk_scored_reports'] ?? 0) }}</h3>
                    <span class="text-muted">{{ $translate('Reports without risk analysis yet') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card admin-index-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-shield-alt mr-2"></i> {{ $translate('Risk distribution') }}
                    </h3>
                </div>
                <div class="card-body">
                    @if ($distributionTotal === 0)
                        <p class="text-muted mb-0">{{ $translate('No submissions yet.') }}</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ $translate('Risk level') }}</th>
                                        <th class="text-right">{{ $translate('Count') }}</th>
                                        <th class="text-right">{{ $translate('Share') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ([
                                        'critical' => $translate('Critical'),
                                        'high' => $translate('High'),
                                        'medium' => $translate('Medium'),
                                        'low' => $translate('Low'),
                                        'unscored' => $translate('Unscored'),
                                    ] as $key => $label)
                                        @php
                                            $count = (int) ($riskDistribution[$key] ?? 0);
                                            $share = $distributionTotal > 0 ? round(($count / $distributionTotal) * 100, 1) : 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $label }}</td>
                                            <td class="text-right">
                                                <span class="badge {{ $heatBadge($count) }}">{{ $count }}</span>
                                            </td>
                                            <td class="text-right text-muted">{{ $share }}%</td>
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
            <div class="card risk-urgent-card h-100">
                <div class="card-header d-flex align-items-center">
                    <span class="risk-icon me-2">⚠️</span>
                    <h2 class="h5 mb-0">{{ $translate('High-risk & urgent snapshot') }}</h2>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h3 class="risk-column-title">{{ $translate('High / Critical') }}</h3>

                            <div class="risk-metric-row">
                                <span class="risk-metric-label">{{ $translate('Total high-risk reports') }}</span>
                                <span class="metric-pill metric-pill--danger">
                                    {{ number_format($highRiskSummary['total'] ?? 0) }}
                                </span>
                            </div>

                            <div class="risk-metric-row">
                                <span class="risk-metric-label">{{ $translate('Open high-risk') }}</span>
                                <span class="metric-pill">
                                    {{ number_format($highRiskSummary['open'] ?? 0) }}
                                </span>
                            </div>

                            <div class="risk-metric-row">
                                <span class="risk-metric-label">{{ $translate('Last 7 days') }}</span>
                                <span class="metric-pill">
                                    {{ number_format($highRiskSummary['last_7_days'] ?? 0) }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h3 class="risk-column-title">{{ $translate('Urgent signals') }}</h3>

                            <div class="risk-metric-row">
                                <span class="risk-metric-label">{{ $translate('Urgent reports') }}</span>
                                <span class="metric-pill metric-pill--warning">
                                    {{ number_format($urgentInsights['total'] ?? 0) }}
                                </span>
                            </div>

                            <div class="risk-metric-row">
                                <span class="risk-metric-label">{{ $translate('Open urgent') }}</span>
                                <span class="metric-pill">
                                    {{ number_format($urgentInsights['open'] ?? 0) }}
                                </span>
                            </div>

                            <div class="risk-metric-row">
                                <span class="risk-metric-label">{{ $translate('High-risk & urgent') }}</span>
                                <span class="metric-pill">
                                    {{ number_format($urgentInsights['high_risk'] ?? 0) }}
                                </span>
                            </div>

                            <div class="risk-metric-row">
                                <span class="risk-metric-label">{{ $translate('Last 7 days') }}</span>
                                <span class="metric-pill">
                                    {{ number_format($urgentInsights['last_7_days'] ?? 0) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card admin-index-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-chart-line mr-2"></i> {{ $translate('30-day trend (Total vs High/Critical vs Urgent)') }}
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
                                        <th class="text-right text-warning">{{ $translate('Urgent') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($timeseries as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td class="text-right">{{ $row['total_reports'] }}</td>
                                            <td class="text-right text-danger">{{ $row['high_risk_reports'] }}</td>
                                            <td class="text-right text-warning">{{ $row['urgent_reports'] }}</td>
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
            <div class="card admin-index-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-th mr-2"></i> {{ $translate('Category heatmap (risk level counts)') }}
                    </h3>
                </div>
                <div class="card-body">
                    @if ($categoryHeatmap->isEmpty())
                        <p class="text-muted mb-0">{{ $translate('No submissions yet.') }}</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ $translate('Category') }}</th>
                                        <th class="text-right text-danger">{{ $translate('Critical') }}</th>
                                        <th class="text-right text-danger">{{ $translate('High') }}</th>
                                        <th class="text-right text-warning">{{ $translate('Medium') }}</th>
                                        <th class="text-right text-info">{{ $translate('Low') }}</th>
                                        <th class="text-right text-muted">{{ $translate('Unscored') }}</th>
                                        <th class="text-right">{{ $translate('Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categoryHeatmap as $category)
                                        <tr>
                                            <td>{{ $displayValue($category->category) ?: __('Unknown') }}</td>
                                            <td class="text-right">
                                                <span class="badge badge-danger">{{ $category->critical }}</span>
                                            </td>
                                            <td class="text-right">
                                                <span class="badge badge-danger">{{ $category->high }}</span>
                                            </td>
                                            <td class="text-right">
                                                <span class="badge badge-warning text-dark">{{ $category->medium }}</span>
                                            </td>
                                            <td class="text-right">
                                                <span class="badge badge-info">{{ $category->low }}</span>
                                            </td>
                                            <td class="text-right">
                                                <span class="badge badge-light text-muted">{{ $category->unscored }}</span>
                                            </td>
                                            <td class="text-right">
                                                <span class="badge badge-primary">{{ $category->total_reports }}</span>
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

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card admin-index-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-user-check mr-2"></i> {{ $translate('Reviewer activity') }}
                    </h3>
                </div>
                <div class="card-body">
                    @if ($reviewerStats->isEmpty())
                        <p class="text-muted mb-0">{{ $translate('No reviewer activity yet.') }}</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ $translate('Reviewer') }}</th>
                                        <th class="text-right">{{ $translate('Resolved') }}</th>
                                        <th class="text-right">{{ $translate('Avg first response (min)') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reviewerStats as $row)
                                        <tr>
                                            <td>{{ $displayValue($row['name']) }}</td>
                                            <td class="text-right">{{ number_format($row['resolved_count']) }}</td>
                                            <td class="text-right">
                                                {{ $row['avg_first_response_min'] !== null ? number_format($row['avg_first_response_min'], 1) : $translate('N/A') }}
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
    </div>
</x-admin-layout>
