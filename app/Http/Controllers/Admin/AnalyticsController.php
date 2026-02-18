<?php

namespace App\Http\Controllers\Admin;

use App\Models\Report;
use App\Models\ReportRiskAnalysis;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends AdminController
{
    /**
     * Display key metrics for reports.
     */
    public function index(Request $request): View
    {
        $filters = $this->filters($request);

        $baseQuery = Report::query();
        $this->scopeByRole($baseQuery);
        $this->applyFilters($baseQuery, $filters);

        $metrics = $this->buildMetrics($baseQuery, $filters);

        $user = auth()->user();
        $orgLabel = $user && $user->hasRole('platform_admin')
            ? 'All organizations'
            : ($user?->org?->name ?? 'My organization');

        return view('admin.analytics.index', [
            'metrics' => $metrics,
            'orgLabel' => $orgLabel,
            'orgOptions' => $this->orgOptions(),
            'filters' => $filters,
        ]);
    }

    /**
     * Extract filter params with safe defaults.
     *
     * @return array<string, mixed>
     */
    protected function filters(Request $request): array
    {
        $cleanString = static fn ($value): string => is_string($value) ? $value : (is_numeric($value) ? (string) $value : '');

        $portal = $cleanString($request->query('portal'));
        $portal = in_array($portal, ['student', 'employee', 'general'], true) ? $portal : '';

        $from = $cleanString($request->query('from'));
        $to = $cleanString($request->query('to'));

        $orgIdParam = $request->query('org_id');
        $orgId = is_numeric($orgIdParam) ? (int) $orgIdParam : null;
        $range = (int) $request->query('range', 30);
        $allowedRanges = [7, 14, 30, 60, 90, 180];
        if (! in_array($range, $allowedRanges, true)) {
            $range = 30;
        }

        return [
            'portal' => $portal,
            'from' => $from,
            'to' => $to,
            'org_id' => $orgId,
            'range' => $range,
        ];
    }

    /**
     * Apply the allowed filters to a query.
     */
    protected function applyFilters($query, array $filters): void
    {
        if ($filters['portal']) {
            $query->where('portal_source', $filters['portal']);
        }

        if ($filters['from']) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }

        if ($filters['to']) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }

        $user = auth()->user();
        $orgId = $filters['org_id'];

        if ($orgId && $user && $user->hasRole('platform_admin')) {
            $query->where('org_id', $orgId);
        }
    }

    /**
     * Build risk-aware metrics.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $baseQuery
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    protected function buildMetrics($baseQuery, array $filters): array
    {
        $rangeDays = (int) ($filters['range'] ?? 30);
        $totalsQuery = clone $baseQuery;

        $totalReports = (clone $totalsQuery)->count();
        $highRiskReports = (clone $totalsQuery)
            ->whereHas('riskAnalysis', fn ($q) => $q->whereIn('risk_level', ['high', 'critical']))
            ->count();
        $lowMediumRiskReports = (clone $totalsQuery)
            ->whereHas('riskAnalysis', fn ($q) => $q->whereIn('risk_level', ['low', 'medium']))
            ->count();
        $noRiskScoredReports = (clone $totalsQuery)
            ->doesntHave('riskAnalysis')
            ->count();
        $urgentOpenReports = (clone $totalsQuery)
            ->where('urgent', true)
            ->where('status', 'open')
            ->count();

        $timeseries = $this->buildTimeseries($baseQuery, $rangeDays);
        $byCategory = $this->buildCategoryBreakdown($baseQuery);

        return [
            'totals' => [
                'total_reports' => $totalReports,
                'high_risk_reports' => $highRiskReports,
                'low_medium_risk_reports' => $lowMediumRiskReports,
                'no_risk_scored_reports' => $noRiskScoredReports,
                'urgent_reports_open' => $urgentOpenReports,
            ],
            'timeseries' => $timeseries,
            'by_category' => $byCategory,
            'risk_distribution' => $this->buildRiskDistribution($baseQuery),
            'high_risk_summary' => $this->buildHighRiskSummary($baseQuery),
            'category_heatmap' => $this->buildCategoryHeatmap($baseQuery),
            'urgent_insights' => $this->buildUrgentInsights($baseQuery),
            'reviewer_stats' => $this->buildReviewerStats($filters),
        ];
    }

    /**
     * Build timeseries data.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $baseQuery
     * @param  int  $rangeDays
     * @return array<int, array<string, mixed>>
     */
    protected function buildTimeseries($baseQuery, int $rangeDays): array
    {
        $range = max(1, min($rangeDays, 120));
        $start = now()->subDays($range - 1)->startOfDay();
        $end = now()->endOfDay();

        $totals = (clone $baseQuery)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $highs = (clone $baseQuery)
            ->whereBetween('reports.created_at', [$start, $end])
            ->join('report_risk_analyses as r', 'r.report_id', '=', 'reports.id')
            ->whereIn('r.risk_level', ['high', 'critical'])
            ->selectRaw('DATE(reports.created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $urgents = (clone $baseQuery)
            ->whereBetween('reports.created_at', [$start, $end])
            ->where('urgent', true)
            ->selectRaw('DATE(reports.created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $dates = collect(range(0, $range - 1))
            ->map(fn ($i) => now()->subDays($i)->toDateString())
            ->reverse()
            ->values();

        return $dates->map(function (string $date) use ($totals, $highs, $urgents): array {
            return [
                'date' => $date,
                'total_reports' => (int) ($totals[$date] ?? 0),
                'high_risk_reports' => (int) ($highs[$date] ?? 0),
                'urgent_reports' => (int) ($urgents[$date] ?? 0),
            ];
        })->all();
    }

    /**
     * Build category-level breakdown including high risk counts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $baseQuery
     * @return \Illuminate\Support\Collection
     */
    protected function buildCategoryBreakdown($baseQuery)
    {
        return (clone $baseQuery)
            ->leftJoin('report_risk_analyses as r', 'r.report_id', '=', 'reports.id')
            ->select([
                'category',
                DB::raw('COUNT(reports.id) as total_reports'),
                DB::raw('SUM(CASE WHEN r.risk_level IN ("high", "critical") THEN 1 ELSE 0 END) as high_risk_count'),
            ])
            ->groupBy('category')
            ->orderByDesc('total_reports')
            ->get();
    }

    /**
     * Risk distribution across all submissions.
     */
    protected function buildRiskDistribution($baseQuery): array
    {
        $row = (clone $baseQuery)
            ->leftJoin('report_risk_analyses as r', 'r.report_id', '=', 'reports.id')
            ->select([
                DB::raw("SUM(CASE WHEN r.risk_level = 'critical' THEN 1 ELSE 0 END) as critical"),
                DB::raw("SUM(CASE WHEN r.risk_level = 'high' THEN 1 ELSE 0 END) as high"),
                DB::raw("SUM(CASE WHEN r.risk_level = 'medium' THEN 1 ELSE 0 END) as medium"),
                DB::raw("SUM(CASE WHEN r.risk_level = 'low' THEN 1 ELSE 0 END) as low"),
                DB::raw('SUM(CASE WHEN r.risk_level IS NULL THEN 1 ELSE 0 END) as unscored'),
            ])
            ->first();

        return [
            'critical' => (int) ($row->critical ?? 0),
            'high' => (int) ($row->high ?? 0),
            'medium' => (int) ($row->medium ?? 0),
            'low' => (int) ($row->low ?? 0),
            'unscored' => (int) ($row->unscored ?? 0),
        ];
    }

    /**
     * Snapshot of high/critical reports.
     */
    protected function buildHighRiskSummary($baseQuery): array
    {
        $recentStart = now()->subDays(6)->startOfDay();

        $total = (clone $baseQuery)
            ->whereHas('riskAnalysis', fn ($q) => $q->whereIn('risk_level', ['high', 'critical']))
            ->count();

        $open = (clone $baseQuery)
            ->where('status', 'open')
            ->whereHas('riskAnalysis', fn ($q) => $q->whereIn('risk_level', ['high', 'critical']))
            ->count();

        $last7Days = (clone $baseQuery)
            ->whereDate('reports.created_at', '>=', $recentStart)
            ->whereHas('riskAnalysis', fn ($q) => $q->whereIn('risk_level', ['high', 'critical']))
            ->count();

        return [
            'total' => $total,
            'open' => $open,
            'last_7_days' => $last7Days,
        ];
    }

    /**
     * Category heatmap with per-level counts.
     */
    protected function buildCategoryHeatmap($baseQuery)
    {
        return (clone $baseQuery)
            ->leftJoin('report_risk_analyses as r', 'r.report_id', '=', 'reports.id')
            ->select([
                'category',
                DB::raw('COUNT(reports.id) as total_reports'),
                DB::raw("SUM(CASE WHEN r.risk_level = 'critical' THEN 1 ELSE 0 END) as critical"),
                DB::raw("SUM(CASE WHEN r.risk_level = 'high' THEN 1 ELSE 0 END) as high"),
                DB::raw("SUM(CASE WHEN r.risk_level = 'medium' THEN 1 ELSE 0 END) as medium"),
                DB::raw("SUM(CASE WHEN r.risk_level = 'low' THEN 1 ELSE 0 END) as low"),
                DB::raw('SUM(CASE WHEN r.risk_level IS NULL THEN 1 ELSE 0 END) as unscored'),
            ])
            ->groupBy('category')
            ->orderByDesc('total_reports')
            ->get();
    }

    /**
     * Urgent-specific insights.
     */
    protected function buildUrgentInsights($baseQuery): array
    {
        $recentStart = now()->subDays(6)->startOfDay();

        $urgentBase = (clone $baseQuery)->where('urgent', true);

        $total = (clone $urgentBase)->count();

        $open = (clone $urgentBase)
            ->where('status', 'open')
            ->count();

        $highRiskUrgent = (clone $urgentBase)
            ->whereHas('riskAnalysis', fn ($q) => $q->whereIn('risk_level', ['high', 'critical']))
            ->count();

        $last7Days = (clone $urgentBase)
            ->whereDate('reports.created_at', '>=', $recentStart)
            ->count();

        return [
            'total' => $total,
            'open' => $open,
            'high_risk' => $highRiskUrgent,
            'last_7_days' => $last7Days,
        ];
    }

    /**
     * Reviewer activity basics: resolved counts and first-response averages.
     */
    protected function buildReviewerStats(array $filters): array
    {
        $user = auth()->user();
        $visibleOrgId = $user?->org_id;
        $orgId = $filters['org_id'];
        $from = $filters['from'] ?: null;
        $to = $filters['to'] ?: null;

        $query = Report::query()
            ->select([
                'resolved_by',
                DB::raw('COUNT(*) as resolved_count'),
                DB::raw('AVG(TIMESTAMPDIFF(MINUTE, reports.created_at, reports.first_response_at)) as avg_first_response_min'),
            ])
            ->whereNotNull('resolved_by');

        if ($from) {
            $query->whereDate('reports.created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('reports.created_at', '<=', $to);
        }

        if ($orgId && $user && $user->hasRole('platform_admin')) {
            $query->where('org_id', $orgId);
        } elseif ($visibleOrgId) {
            $query->where('org_id', $visibleOrgId);
        }

        $rows = $query
            ->groupBy('resolved_by')
            ->orderByDesc('resolved_count')
            ->limit(10)
            ->get();

        $users = User::whereIn('id', $rows->pluck('resolved_by'))->get()->keyBy('id');

        return $rows->map(function ($row) use ($users): array {
            $user = $users[$row->resolved_by] ?? null;
            return [
                'user_id' => $row->resolved_by,
                'name' => $user?->name ?? __('Unknown'),
                'resolved_count' => (int) $row->resolved_count,
                'avg_first_response_min' => $row->avg_first_response_min !== null
                    ? round((float) $row->avg_first_response_min, 1)
                    : null,
            ];
        })->all();
    }
}
