<?php

namespace App\Services;

use App\Models\ReportFile;
use Illuminate\Support\Facades\Log;

class AudioTranscriptionService
{
    /**
     * Transcribe an audio report file using an external Python script.
     *
     * @return array{status:string,transcript:?string,error:?string,hits:array<int,string>}
     */
    public function transcribe(ReportFile $file): array
    {
        $path = $this->resolvePath($file);

        if (! $path) {
            Log::warning('Audio transcription skipped; file missing.', [
                'file_id' => $file->getKey(),
            ]);
            return ['status' => 'failed', 'transcript' => null, 'error' => 'file_missing'];
        }

        $python = config('asylon.audio_transcription.python_path', '/usr/bin/python3');
        $script = config('asylon.audio_transcription.script_path', '/var/www/scripts/transcribe_server.py');

        $command = escapeshellcmd($python).' '.escapeshellarg($script).' '.escapeshellarg($path);

        Log::info('Audio transcription invoking python.', [
            'file_id' => $file->getKey(),
            'path' => $path,
            'python' => $python,
            'script' => $script,
        ]);

        $output = @shell_exec($command);
        $transcript = $output !== null ? trim((string) $output) : null;

        if ($transcript === null || $transcript === '') {
            Log::warning('Audio transcription returned empty output.', [
                'file_id' => $file->getKey(),
                'command' => $command,
            ]);

            return ['status' => 'failed', 'transcript' => null, 'error' => 'empty_output'];
        }

        if (str_starts_with(strtolower($transcript), 'error:')) {
            Log::warning('Audio transcription error reported.', [
                'file_id' => $file->getKey(),
                'output' => $transcript,
            ]);

            return ['status' => 'failed', 'transcript' => null, 'error' => $transcript];
        }

        $hits = $this->keywordHits($transcript);

        Log::info('Audio transcription completed.', [
            'file_id' => $file->getKey(),
            'hits' => $hits,
            'transcript_preview' => mb_substr($transcript, 0, 300),
        ]);

        return ['status' => 'completed', 'transcript' => $transcript, 'error' => null, 'hits' => $hits];
    }

    /**
     * Resolve the absolute path to the stored audio file, preferring anonymized audio.
     */
    protected function resolvePath(ReportFile $file): ?string
    {
        $relative = $file->anonymized_path ?: $file->path;
        if (! $relative) {
            return null;
        }

        $full = storage_path('app/public/'.$relative);

        return is_file($full) ? $full : null;
    }

    /**
     * Find risky keyword hits in the transcript.
     *
     * @return array<int, string>
     */
    protected function keywordHits(string $transcript): array
    {
        $haystack = mb_strtolower($transcript);

        $keywords = [
            'knife', 'gun', 'weapon', 'bomb', 'shoot', 'stab',
            'sex', 'sexual', 'nudity', 'nude', 'porn', 'explicit', 'harassment',
            'self harm', 'suicide', 'kill myself', 'cutting', 'overdose',
            'threat', 'violence', 'blood', 'gore',
        ];

        $hits = [];
        foreach ($keywords as $keyword) {
            if (str_contains($haystack, $keyword)) {
                $hits[] = $keyword;
            }
        }

        return array_values(array_unique($hits));
    }
}
