<?php

namespace App\Http\Controllers;

use App\Events\ReportSubmitted;
use App\Http\Requests\PostChatMessageRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ReportFile;
use App\Models\AuditLog;
use App\Models\User;
use App\Notifications\FirstResponseNotification;
use App\Services\Audit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
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
            'resolver',
        ]);

        Audit::log('reviewer', 'view_report', 'report', $report->getKey());

        return view('reviews.show', [
            'report' => $report,
            'timeline' => $this->buildTimeline($report),
            'reviewers' => $this->reviewersForOrg($user, $report->org_id),
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

        $isFirstResponse = ! $report->first_response_at;

        if (! $report->first_response_at) {
            $report->first_response_at = now();
            $report->save();
        }

        Audit::log('reviewer', 'post_message', 'report', $report->getKey());

        if ($isFirstResponse) {
            $this->notifyFirstResponse($report, $user, $this->baseUrl($request));
            Audit::log('reviewer', 'first_response', 'report', $report->getKey(), [
                'responder_id' => $user->getKey(),
                'responder_name' => $user->name,
            ]);
        }

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
            'note' => ['nullable', 'string', 'max:2000'],
            'resolved_by' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $fromStatus = $report->status;
        $report->status = $validated['status'];
        $report->status_note = $validated['note'] ?? null;

        $resolvedBy = null;
        $resolvedById = $validated['resolved_by'] ?? null;

        if ($report->status === 'closed') {
            if ($resolvedById) {
                $resolvedBy = User::query()
                    ->when(! $user->hasRole('platform_admin'), fn ($q) => $q->where('org_id', $report->org_id))
                    ->where('active', true)
                    ->find($resolvedById);

                if (! $resolvedBy) {
                    return back()->withErrors(['resolved_by' => __('Reviewer not found for this organization.')]);
                }

                $report->resolved_by = $resolvedBy->getKey();
            } elseif ($user->hasRole(['reviewer', 'security_lead', 'org_admin'])) {
                $report->resolved_by = $user->getKey();
                $resolvedBy = $user;
            }
        } else {
            // Clear resolver if reopening.
            $report->resolved_by = null;
        }

        $report->save();

        Audit::log('reviewer', 'change_status', 'report', $report->getKey(), [
            'from' => $fromStatus,
            'to' => $report->status,
            'note' => $report->status_note,
            'resolved_by' => $resolvedBy?->getKey(),
            'resolved_by_name' => $resolvedBy?->name,
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

    /**
     * Resolve reviewers/security leads/org admins for the status form.
     */
    protected function reviewersForOrg($user, int $orgId)
    {
        return User::query()
            ->where('active', true)
            ->when(! $user->hasRole('platform_admin'), fn ($q) => $q->where('org_id', $orgId))
            ->whereIn('role', ['reviewer', 'security_lead', 'org_admin'])
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * Base URL helper for absolute links.
     */
    protected function baseUrl(Request $request): string
    {
        $root = trim((string) ($request->root() ?: config('app.url', 'http://localhost')));

        return $root === '' ? 'http://localhost' : rtrim($root, '/');
    }

    /**
     * Notify org admins when the first response is sent.
     */
    protected function notifyFirstResponse(Report $report, $responder, string $baseUrl): void
    {
        if (! config('asylon.notifications.first_response_org_admin', true)) {
            return;
        }

        $admins = User::query()
            ->where('org_id', $report->org_id)
            ->where('active', true)
            ->where('role', 'org_admin')
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        Notification::send(
            $admins,
            new FirstResponseNotification($report, $baseUrl, $responder?->name)
        );
    }

    /**
     * Build a simplified activity timeline for the report.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    protected function buildTimeline(Report $report): Collection
    {
        return AuditLog::query()
            ->with('user')
            ->where('target_type', 'report')
            ->where('target_id', $report->getKey())
            ->orderBy('created_at')
            ->get()
            ->map(function (AuditLog $log): array {
                return [
                    'id' => $log->getKey(),
                    'time' => $log->created_at,
                    'title' => $this->timelineTitle($log),
                    'description' => $this->timelineDescription($log),
                    'icon' => $this->timelineIcon($log),
                ];
            });
    }

    protected function timelineTitle(AuditLog $log): string
    {
        return match ($log->action) {
            'portal_submission' => __('Report submitted'),
            'alert_dispatched' => __('Urgent alert sent'),
            'view_report' => __('Report viewed'),
            'update_report' => __('Report details updated'),
            'change_status' => __('Status changed'),
            'post_message' => __('Reviewer replied'),
            'post_followup_message' => __('Reporter follow-up'),
            'first_response' => __('First response sent'),
            'reporter_followup_alert' => __('Reporter follow-up alerts sent'),
            'download_attachment' => __('Attachment downloaded'),
            'preview_attachment' => __('Attachment previewed'),
            'download_followup_attachment' => __('Reporter downloaded attachment'),
            'preview_followup_attachment' => __('Reporter previewed attachment'),
            'trash_report' => __('Report moved to trash'),
            'restore_report' => __('Report restored from trash'),
            default => Str::headline(str_replace('_', ' ', $log->action)),
        };
    }

    protected function timelineDescription(AuditLog $log): string
    {
        $actor = $this->timelineActor($log);
        $meta = $log->meta ?? [];

        return match ($log->action) {
            'portal_submission' => __('Submitted via :portal (:type)', [
                'portal' => $meta['portal_source'] ?? __('portal'),
                'type' => $meta['type'] ?? __('unspecified'),
            ]),
            'alert_dispatched' => __('Urgent alert dispatched (:channel)', [
                'channel' => $meta['channel'] ?? __('unknown channel'),
            ]),
            'update_report' => __('Updated by :actor', ['actor' => $actor]),
            'change_status' => $this->buildStatusChangeDescription($actor, $meta),
            'post_message' => __('Reply sent by :actor', ['actor' => $actor]),
            'post_followup_message' => __('Reporter shared a follow-up message'),
            'first_response' => __('First response sent by :actor', ['actor' => $actor]),
            'reporter_followup_alert' => __('Alerts sent (emails: :emails, sms: :sms)', [
                'emails' => $meta['emails_sent'] ?? 0,
                'sms' => $meta['sms_sent'] ?? 0,
            ]),
            'download_attachment', 'preview_attachment' => __('Attachment access by :actor', ['actor' => $actor]),
            'download_followup_attachment', 'preview_followup_attachment' => __('Reporter accessed attachment'),
            'trash_report' => __('Moved to trash by :actor', ['actor' => $actor]),
            'restore_report' => __('Restored by :actor', ['actor' => $actor]),
            default => $actor ? __('Action by :actor', ['actor' => $actor]) : __('Activity logged'),
        };
    }

    protected function timelineIcon(AuditLog $log): string
    {
        return match ($log->action) {
            'portal_submission' => 'fas fa-flag',
            'alert_dispatched' => 'fas fa-bell',
            'view_report' => 'fas fa-eye',
            'update_report' => 'fas fa-edit',
            'change_status' => 'fas fa-random',
            'post_message' => 'fas fa-reply',
            'post_followup_message' => 'fas fa-comment-dots',
            'first_response' => 'fas fa-check-circle',
            'reporter_followup_alert' => 'fas fa-paper-plane',
            'download_attachment', 'preview_attachment' => 'fas fa-paperclip',
            'download_followup_attachment', 'preview_followup_attachment' => 'fas fa-paperclip',
            'trash_report' => 'fas fa-trash',
            'restore_report' => 'fas fa-undo',
            default => 'fas fa-circle',
        };
    }

    protected function buildStatusChangeDescription(?string $actor, array $meta): string
    {
        $base = __('Status changed from :from to :to by :actor', [
            'from' => $meta['from'] ?? __('unknown'),
            'to' => $meta['to'] ?? __('unknown'),
            'actor' => $actor ?? __('System'),
        ]);

        $resolvedBy = $meta['resolved_by_name'] ?? null;
        $note = $meta['note'] ?? null;

        $parts = [$base];

        if ($resolvedBy) {
            $parts[] = __('Resolved by :name', ['name' => $resolvedBy]);
        }

        if ($note) {
            $parts[] = __('Note: :note', ['note' => $note]);
        }

        return implode(' — ', $parts);
    }

    protected function timelineActor(AuditLog $log): ?string
    {
        if ($log->user?->name) {
            return $log->user->name;
        }

        return match ($log->actor_type) {
            'reviewer' => __('Reviewer'),
            'reporter' => __('Reporter'),
            'system' => __('System'),
            default => null,
        };
    }
}
