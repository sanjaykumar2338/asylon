<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostChatMessageRequest;
use App\Models\Report;
use App\Models\ReportFile;
use App\Services\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ChatController extends Controller
{
    /**
     * Display the anonymous conversation thread.
     */
    public function thread(string $token): View
    {
        $report = Report::where('chat_token', $token)
            ->with([
                'files',
                'messages' => fn ($query) => $query->orderBy('created_at'),
            ])
            ->firstOrFail();

        $messages = $report->messages->values();

        Audit::log('reporter', 'view_chat', 'report', $report->getKey());

        return view('chat.thread', compact('report', 'messages'));
    }

    /**
     * Post a message from the reporter side.
     */
    public function post(PostChatMessageRequest $request, string $token): RedirectResponse
    {
        $report = Report::where('chat_token', $token)->firstOrFail();

        $report->messages()->create([
            'from' => 'reporter',
            'body' => $request->input('body'),
        ]);

        Audit::log('reporter', 'post_message', 'report', $report->getKey());

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

        Audit::log('reporter', 'preview_attachment', 'report', $report->getKey(), [
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

        Audit::log('reporter', 'download_attachment', 'report', $report->getKey(), [
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
}
