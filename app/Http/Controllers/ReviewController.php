<?php

namespace App\Http\Controllers;

use App\Events\ReportSubmitted;
use App\Http\Requests\PostChatMessageRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ReportFile;
use App\Services\Audit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Show reports requiring attention.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $status = (string) $request->query('status', '');
        $urgent = (string) $request->query('urgent', '');
        $category = (string) $request->query('category', '');
        $subcategory = (string) $request->query('subcategory', '');
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');
        $type = (string) $request->query('type', '');
        $severity = (string) $request->query('severity', '');
        $sort = (string) $request->query('sort', 'submitted_desc');

        $query = Report::query()
            ->with(['org', 'files'])
            ->withCount('files');

        if (! $user->can('view-all')) {
            $query->where('org_id', $user->org_id);
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($urgent !== '') {
            $query->where('urgent', $urgent === '1');
        }

        if ($category !== '') {
            $query->where('category', $category);
        }

        if ($subcategory !== '') {
            $query->where('subcategory', $subcategory);
        }

        if ($type !== '') {
            $query->where('type', $type);
        }

        if ($severity !== '') {
            $query->where('severity', $severity);
        }

        if ($from !== '' && Carbon::hasFormat($from, 'Y-m-d')) {
            $query->whereDate('created_at', '>=', Carbon::createFromFormat('Y-m-d', $from));
        }

        if ($to !== '' && Carbon::hasFormat($to, 'Y-m-d')) {
            $query->whereDate('created_at', '<=', Carbon::createFromFormat('Y-m-d', $to));
        }

        $reports = $this->applySorting($query, $sort)
            ->paginate(20)
            ->withQueryString();

        $categoriesMap = ReportCategory::query()
            ->with('subcategories')
            ->orderBy('position')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function (ReportCategory $categoryModel): array {
                $subcategories = $categoryModel->subcategories
                    ->map(fn ($sub) => $sub->name)
                    ->all();

                return [$categoryModel->name => $subcategories];
            })
            ->toArray();

        $subcategoryOptions = $category !== '' ? ($categoriesMap[$category] ?? []) : [];

        return view('reviews.index', [
            'reports' => $reports,
            'status' => $status,
            'urgent' => $urgent,
            'category' => $category,
            'subcategory' => $subcategory,
            'type' => $type,
            'severity' => $severity,
            'from' => $from,
            'to' => $to,
            'sort' => $sort,
            'categoriesMap' => $categoriesMap,
            'subcategoryOptions' => $subcategoryOptions,
        ]);
    }

    /**
     * Apply sorting to the review query.
     */
    protected function applySorting(Builder $query, string $sort): Builder
    {
        return match ($sort) {
            'submitted_asc' => $query->orderBy('created_at', 'asc'),
            'violation_desc' => $query
                ->orderByRaw('violation_date IS NULL')
                ->orderBy('violation_date', 'desc')
                ->orderBy('created_at', 'desc'),
            'violation_asc' => $query
                ->orderByRaw('violation_date IS NULL')
                ->orderBy('violation_date', 'asc')
                ->orderBy('created_at', 'desc'),
            'category_asc' => $query
                ->orderBy('category')
                ->orderBy('subcategory')
                ->orderBy('created_at', 'desc'),
            'category_desc' => $query
                ->orderBy('category', 'desc')
                ->orderBy('subcategory', 'desc')
                ->orderBy('created_at', 'desc'),
            'subcategory_asc' => $query
                ->orderBy('subcategory')
                ->orderBy('category')
                ->orderBy('created_at', 'desc'),
            'subcategory_desc' => $query
                ->orderBy('subcategory', 'desc')
                ->orderBy('category', 'desc')
                ->orderBy('created_at', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };
    }

    /**
     * Display a detailed view of a report.
     */
    public function show(Request $request, Report $report): View
    {
        $user = $request->user();

        if (! $user->hasRole('platform_admin') && $user->org_id !== $report->org_id) {
            abort(403);
        }

        $report->load([
            'org',
            'files',
            'messages' => fn ($query) => $query->orderBy('sent_at'),
        ]);

        Audit::log('reviewer', 'view_report', 'report', $report->getKey());

        return view('reviews.show', [
            'report' => $report,
        ]);
    }

    /**
     * Show the edit form for a report.
     */
    public function edit(Request $request, Report $report): View
    {
        $user = $request->user();

        if (! $user->hasRole('platform_admin') && $user->org_id !== $report->org_id) {
            abort(403);
        }

        return view('reviews.edit', [
            'report' => $report,
            'categories' => ReportCategory::query()
                ->with('subcategories')
                ->orderBy('position')
                ->orderBy('name')
                ->get()
                ->mapWithKeys(function (ReportCategory $categoryModel): array {
                    $subcategories = $categoryModel->subcategories
                        ->map(fn ($sub) => $sub->name)
                        ->all();

                    return [$categoryModel->name => $subcategories];
                })
                ->toArray(),
        ]);
    }

    /**
     * Update a report details.
     */
    public function update(UpdateReportRequest $request, Report $report): RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasRole('platform_admin') && $user->org_id !== $report->org_id) {
            abort(403);
        }

        $validated = $request->validated();
        $wasUrgent = (bool) $report->urgent;

        $report->category = $validated['category'];
        $report->subcategory = $validated['subcategory'];
        $report->description = $validated['description'];
        $report->violation_date = $validated['violation_date'] ?? null;
        $report->contact_name = $validated['contact_name'] ?? null;
        $report->contact_email = $validated['contact_email'] ?? null;
        $report->contact_phone = $validated['contact_phone'] ?? null;
        $report->urgent = $validated['urgent'] ?? false;
        $report->status = $validated['status'];
        $report->save();

        Audit::log('reviewer', 'update_report', 'report', $report->getKey(), [
            'status' => $report->status,
            'urgent' => $report->urgent,
        ]);

        if (! $wasUrgent && $report->urgent) {
            event(new ReportSubmitted(
                $report->fresh(['org']),
                config('app.url', 'http://localhost')
            ));
        }

        return redirect()
            ->route('reports.show', $report)
            ->with('ok', 'Report updated successfully.');
    }

    /**
     * Send a reply from a reviewer to the reporter.
     */
    public function messageReporter(PostChatMessageRequest $request, Report $report): RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasRole('platform_admin') && $user->org_id !== $report->org_id) {
            abort(403);
        }

        $report->messages()->create([
            'side' => 'reviewer',
            'message' => $request->input('message'),
            'sent_at' => now(),
        ]);

        if (! $report->first_response_at) {
            $report->first_response_at = now();
            $report->save();
        }

        Audit::log('reviewer', 'post_message', 'report', $report->getKey());

        return back()->with('ok', 'Reply sent.');
    }

    /**
     * Update the status of a report.
     */
    public function updateStatus(Request $request, Report $report): RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasRole('platform_admin') && $user->org_id !== $report->org_id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:open,in_review,closed'],
        ]);

        $fromStatus = $report->status;
        $report->status = $validated['status'];
        $report->save();

        Audit::log('reviewer', 'change_status', 'report', $report->getKey(), [
            'from' => $fromStatus,
            'to' => $report->status,
        ]);

        return back()->with('ok', 'Status updated.');
    }

    /**
     * Download a report attachment via signed route.
     */
    public function downloadFile(Request $request, Report $report, ReportFile $file)
    {
        $user = $request->user();

        if (! $user->hasRole('platform_admin') && $user->org_id !== $report->org_id) {
            abort(403);
        }

        if ($file->report_id !== $report->getKey()) {
            abort(404);
        }

        if (! Storage::disk('public')->exists($file->path)) {
            abort(404);
        }

        Audit::log('reviewer', 'download_attachment', 'report', $report->getKey(), [
            'file_id' => $file->getKey(),
        ]);

        return Storage::disk('public')->download(
            $file->path,
            $file->original_name,
            array_filter([
                'Content-Type' => $file->mime,
            ])
        );
    }

    /**
     * Stream an attachment inline for preview.
     */
    public function previewFile(Request $request, Report $report, ReportFile $file)
    {
        $user = $request->user();

        if (! $user->hasRole('platform_admin') && $user->org_id !== $report->org_id) {
            abort(403);
        }

        if ($file->report_id !== $report->getKey()) {
            abort(404);
        }

        if (! Storage::disk('public')->exists($file->path)) {
            abort(404);
        }

        Audit::log('reviewer', 'preview_attachment', 'report', $report->getKey(), [
            'file_id' => $file->getKey(),
        ]);

        return Storage::disk('public')->response(
            $file->path,
            $file->original_name,
            array_filter([
                'Content-Type' => $file->mime,
            ])
        );
    }

    /**
     * Soft delete (trash) a report.
     */
    public function destroy(Request $request, Report $report): RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasRole('platform_admin') && $user->org_id !== $report->org_id) {
            abort(403);
        }

        if ($report->trashed()) {
            return redirect()
                ->route('reviews.index')
                ->with('ok', 'Report already in trash.');
        }

        $report->delete();

        Audit::log('reviewer', 'trash_report', 'report', $report->getKey());

        return redirect()
            ->route('reviews.index')
            ->with('ok', 'Report moved to trash.');
    }
}
