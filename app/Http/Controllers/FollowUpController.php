<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostChatMessageRequest;
use App\Jobs\SendReporterFollowupNotifications;
use App\Models\Report;
use App\Models\ReportFile;
use App\Services\Audit;
use App\Support\LocaleManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\View\View;

class FollowUpController extends Controller
{
    /**
     * Display the public-facing follow-up portal for a report.
     */
    public function entry(): View
    {
        return view('followup.entry');
    }

    /**
     * Accept a case ID/token and redirect to the follow-up portal.
     */
    public function redirectFromEntry(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'case_id' => ['required', 'string', 'max:100'],
        ]);

        $caseId = $this->extractCaseId((string) ($validated['case_id'] ?? ''));

        $report = Report::query()
            ->where('chat_token', $caseId)
            ->orWhere('id', $caseId)
            ->first();

        if (! $report) {
            return Redirect::back()
                ->withErrors(['case_id' => __('followup.entry.not_found')])
                ->withInput();
        }

        return Redirect::route('followup.show', $report->chat_token);
    }

    public function show(string $token): View
    {
        $report = Report::where('chat_token', $token)
            ->with([
                'files',
                'org',
                'messages' => fn ($query) => $query->orderBy('sent_at'),
            ])
            ->firstOrFail();

        $messages = $report->messages->values();
        LocaleManager::applyOrgLocale($report->org);

        Audit::log('reporter', 'view_followup_portal', 'report', $report->getKey());

        return view('followup.show', [
            'report' => $report,
            'messages' => $messages,
        ]);
    }

    /**
     * Post a follow-up message from the reporter side.
     */
    public function storeMessage(
        PostChatMessageRequest $request,
        string $token
    ): RedirectResponse {
        $report = Report::where('chat_token', $token)->with('org')->firstOrFail();
        LocaleManager::applyOrgLocale($report->org);

        $message = $report->messages()->create([
            'side' => 'reporter',
            'message' => $request->input('message'),
            'sent_at' => now(),
        ]);

        try {
            SendReporterFollowupNotifications::dispatch(
                $report,
                $message,
                $this->baseUrl($request)
            );
        } catch (Throwable $e) {
            Log::error('Reporter follow-up notification dispatch failed.', [
                'report_id' => $report->getKey(),
                'message_id' => $message->getKey(),
                'exception' => $e,
            ]);
        }

        Audit::log('reporter', 'post_followup_message', 'report', $report->getKey(), [
            'message_id' => $message->getKey(),
        ]);

        return Redirect::back()->with('ok', __('followup.show.message_sent'));
    }

    /**
     * Stream an attachment inline for the reporter.
     */
    public function previewAttachment(string $token, ReportFile $file)
    {
        $report = Report::where('chat_token', $token)->with('org')->firstOrFail();
        LocaleManager::applyOrgLocale($report->org);

        if ($file->report_id !== $report->getKey()) {
            abort(404);
        }

        Audit::log('reporter', 'preview_followup_attachment', 'report', $report->getKey(), [
            'file_id' => $file->getKey(),
        ]);

        $storage = Storage::disk('public');
        $path = $this->preferredFilePath($file, $storage);

        if (! $storage->exists($path)) {
            abort(404);
        }

        return $storage->response(
            $path,
            $file->original_name,
            array_filter([
                'Content-Type' => $file->mime,
            ])
        );
    }

    /**
     * Download an attachment for the reporter.
     */
    public function downloadAttachment(string $token, ReportFile $file)
    {
        $report = Report::where('chat_token', $token)->with('org')->firstOrFail();
        LocaleManager::applyOrgLocale($report->org);

        if ($file->report_id !== $report->getKey()) {
            abort(404);
        }

        Audit::log('reporter', 'download_followup_attachment', 'report', $report->getKey(), [
            'file_id' => $file->getKey(),
        ]);

        $storage = Storage::disk('public');
        $path = $this->preferredFilePath($file, $storage);

        if (! $storage->exists($path)) {
            abort(404);
        }

        return $storage->download(
            $path,
            $file->original_name,
            array_filter([
                'Content-Type' => $file->mime,
            ])
        );
    }

    /**
     * Determine the base URL for notification links.
     */
    protected function baseUrl(Request $request): string
    {
        $root = trim((string) ($request->root() ?: config('app.url', 'http://localhost')));

        return $root === '' ? 'http://localhost' : rtrim($root, '/');
    }

    /**
     * Normalize pasted case IDs, even if a full follow-up URL is provided.
     */
    protected function extractCaseId(string $raw): string
    {
        $value = trim($raw);

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $path = parse_url($value, PHP_URL_PATH) ?? '';
            $segments = array_values(array_filter(explode('/', $path)));

            if (! empty($segments)) {
                return (string) end($segments);
            }
        }

        return $value;
    }

    /**
     * Prefer anonymized audio when available.
     */
    protected function preferredFilePath(ReportFile $file, $storage): string
    {
        if ($file->isAudio() && $file->anonymized_path && $storage->exists($file->anonymized_path)) {
            return $file->anonymized_path;
        }

        return $file->path;
    }
}
