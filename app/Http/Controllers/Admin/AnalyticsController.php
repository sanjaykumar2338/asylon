<?php

namespace App\Http\Controllers\Admin;

use App\Models\Report;
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

        $total = (clone $baseQuery)->count();
        $urgent = (clone $baseQuery)->where('urgent', true)->count();
        $urgentPercent = $total > 0 ? round(($urgent / $total) * 100, 1) : 0.0;

        $byCategory = (clone $baseQuery)
            ->select('category', DB::raw('COUNT(*) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $bySubcategory = (clone $baseQuery)
            ->select('category', 'subcategory', DB::raw('COUNT(*) as total'))
            ->whereNotNull('subcategory')
            ->groupBy('category', 'subcategory')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $byType = (clone $baseQuery)
            ->select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->get();

        $bySeverity = (clone $baseQuery)
            ->select('severity', DB::raw('COUNT(*) as total'))
            ->groupBy('severity')
            ->get();

        $byOrg = (clone $baseQuery)
            ->select('org_id', DB::raw('COUNT(*) as total'))
            ->with('org:id,name')
            ->groupBy('org_id')
            ->orderByDesc('total')
            ->get();

        $avgResponse = (clone $baseQuery)
            ->whereNotNull('first_response_at')
            ->avg(DB::raw('TIMESTAMPDIFF(MINUTE, created_at, first_response_at)'));

        $avgResponse = $avgResponse ? round($avgResponse, 1) : null;

        $user = auth()->user();
        $orgLabel = $user && $user->hasRole('platform_admin')
            ? 'All organizations'
            : ($user?->org?->name ?? 'My organization');

        \Log::debug('Analytics data payload', [
            'total' => $total,
            'urgent' => $urgent,
            'filters' => $filters,
            'by_category_sample' => $byCategory->first(),
        ]);

        return view('admin.analytics.index', [
            'total' => $total,
            'urgent' => $urgent,
            'urgentPercent' => $urgentPercent,
            'byCategory' => $byCategory,
            'bySubcategory' => $bySubcategory,
            'byType' => $byType,
            'bySeverity' => $bySeverity,
            'byOrg' => $byOrg,
            'avgResponse' => $avgResponse,
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

        return [
            'portal' => $portal,
            'from' => $from,
            'to' => $to,
            'org_id' => $orgId,
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
}
