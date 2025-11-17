<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostChatMessageRequest;
use App\Jobs\SendReporterFollowupNotifications;
use App\Models\Report;
use App\Models\ReportFile;
use App\Services\Audit;
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
    public function show(string $token): View
    {
        $report = Report::where('chat_token', $token)
            ->with([
                'files',
                'messages' => fn ($query) => $query->orderBy('sent_at'),
            ])
            ->firstOrFail();

        $messages = $report->messages->values();

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
        $report = Report::where('chat_token', $token)->firstOrFail();

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

        return Redirect::back()->with('ok', 'Message sent.');
    }

    /**
     * Stream an attachment inline for the reporter.
     */
    public function previewAttachment(string $token, ReportFile $file)
    {
        $report = Report::where('chat_token', $token)->firstOrFail();

        if ($file->report_id !== $report->getKey()) {
            abort(404);
        }

        Audit::log('reporter', 'preview_followup_attachment', 'report', $report->getKey(), [
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
     * Download an attachment for the reporter.
     */
    public function downloadAttachment(string $token, ReportFile $file)
    {
        $report = Report::where('chat_token', $token)->firstOrFail();

        if ($file->report_id !== $report->getKey()) {
            abort(404);
        }

        Audit::log('reporter', 'download_followup_attachment', 'report', $report->getKey(), [
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
     * Determine the base URL for notification links.
     */
    protected function baseUrl(Request $request): string
    {
        $root = trim((string) ($request->root() ?: config('app.url', 'http://localhost')));

        return $root === '' ? 'http://localhost' : rtrim($root, '/');
    }
}
