<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\Audit;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TrashReportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $query = Report::onlyTrashed()
            ->with('org')
            ->orderByDesc('deleted_at');

        if (! $user->can('view-all')) {
            $query->where('org_id', $user->org_id);
        }

        $reports = $query
            ->paginate(20)
            ->withQueryString();

        return view('reviews.trash', [
            'reports' => $reports,
        ]);
    }

    /**
     * Restore a trashed report.
     */
    public function restore(Request $request, Report $report): RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasRole('platform_admin') && $user->org_id !== $report->org_id) {
            abort(403);
        }

        if (! $report->trashed()) {
            return redirect()
                ->route('reviews.trash')
                ->with('ok', __('Report already active.'));
        }

        $report->restore();

        Audit::log('reviewer', 'restore_report', 'report', $report->getKey());

        return redirect()
            ->route('reviews.trash')
            ->with('ok', __('Report restored successfully.'));
    }
}
