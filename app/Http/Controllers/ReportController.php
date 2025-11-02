<?php

namespace App\Http\Controllers;

use App\Events\ReportSubmitted;
use App\Http\Requests\StoreReportRequest;
use App\Models\Org;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Show the anonymous report submission form.
     */
    public function create(): View
    {
        $orgs = Org::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('report.create', compact('orgs'));
    }

    /**
     * Store a newly created report.
     */
    public function store(StoreReportRequest $request): RedirectResponse
    {
        $report = DB::transaction(function () use ($request): Report {
            $validated = $request->validated();
            $attachments = $validated['attachments'] ?? [];
            $voiceComment = $validated['voice_comment'] ?? null;
            unset($validated['attachments']);
            unset($validated['voice_comment']);

            $report = Report::create($validated);
            $report->chat_token = (string) Str::uuid();
            $report->save();

            foreach ($attachments as $index => $attachment) {
                $file = $request->file("attachments.$index.file");

                if (! $file) {
                    continue;
                }

                $comment = isset($attachment['comment']) ? trim((string) $attachment['comment']) : '';
                $comment = $comment === '' ? null : $comment;
                $storedPath = $file->store("reports/{$report->getKey()}", 'public');

                $report->files()->create([
                    'path' => $storedPath,
                    'original_name' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'comment' => $comment,
                ]);
            }

            $voiceFile = $request->file('voice_recording');
            if ($voiceFile) {
                $normalizedVoiceComment = is_string($voiceComment) ? trim($voiceComment) : '';
                $normalizedVoiceComment = $normalizedVoiceComment === '' ? null : $normalizedVoiceComment;
                $storedPath = $voiceFile->store("reports/{$report->getKey()}", 'public');

                $report->files()->create([
                    'path' => $storedPath,
                    'original_name' => $voiceFile->getClientOriginalName(),
                    'mime' => $voiceFile->getMimeType(),
                    'size' => $voiceFile->getSize(),
                    'comment' => $normalizedVoiceComment,
                ]);
            }

            return $report;
        });

        event(new ReportSubmitted($report));

        return Redirect::route('report.thanks', $report->getKey());
    }

    /**
     * Display the thank you page once a report is submitted.
     */
    public function thanks(string $id): View
    {
        $report = Report::findOrFail($id);

        return view('report.thanks', [
            'id' => $id,
            'chatToken' => $report->chat_token,
        ]);
    }
}
