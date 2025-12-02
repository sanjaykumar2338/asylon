<?php

namespace App\Jobs;

use App\Models\ReportFile;
use App\Services\AudioTranscriptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TranscribeAudioJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 180;

    public function __construct(public ReportFile $file)
    {
    }

    public function handle(AudioTranscriptionService $transcriber): void
    {
        $file = $this->file->fresh();

        if (! $file || ! $file->isAudio()) {
            return;
        }

        $result = $transcriber->transcribe($file);

        $file->forceFill([
            'transcription_status' => $result['status'],
            'transcript' => $result['transcript'],
        ])->save();

        if (($result['status'] ?? '') === 'failed' && ($result['error'] ?? null)) {
            \Log::warning('Audio transcription failed.', [
                'file_id' => $file->getKey(),
                'error' => $result['error'],
            ]);
        }
    }
}
