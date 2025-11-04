<?php

namespace App\Http\Controllers\Admin;

use App\Models\Report;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends AdminController
{
    /**
     * Display key metrics for reports.
     */
    public function index(): View
    {
        $baseQuery = Report::query();
        $this->scopeByRole($baseQuery);

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

        $avgResponse = (clone $baseQuery)
            ->whereNotNull('first_response_at')
            ->avg(DB::raw('TIMESTAMPDIFF(MINUTE, created_at, first_response_at)'));

        $avgResponse = $avgResponse ? round($avgResponse, 1) : null;

        $user = auth()->user();
        $orgLabel = $user && $user->hasRole('platform_admin')
            ? 'All organizations'
            : ($user?->org?->name ?? 'My organization');

        return view('admin.analytics.index', [
            'total' => $total,
            'urgent' => $urgent,
            'urgentPercent' => $urgentPercent,
            'byCategory' => $byCategory,
            'bySubcategory' => $bySubcategory,
            'avgResponse' => $avgResponse,
            'orgLabel' => $orgLabel,
        ]);
    }
}
