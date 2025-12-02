<?php

namespace App\Services;

use App\Models\ReportFile;
use Illuminate\Support\Facades\Log;

class AudioTranscriptionService
{
    /**
     * Transcribe an audio report file using an external Python script.
     *
     * @return array{status:string,transcript:?string,error:?string}
     */
    public function transcribe(ReportFile $file): array
    {
        $path = $this->resolvePath($file);

        if (! $path) {
            return ['status' => 'failed', 'transcript' => null, 'error' => 'file_missing'];
        }

        $python = config('asylon.audio_transcription.python_path', '/usr/bin/python3');
        $script = config('asylon.audio_transcription.script_path', '/var/www/scripts/transcribe_server.py');

        $command = escapeshellcmd($python).' '.escapeshellarg($script).' '.escapeshellarg($path);

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

        return ['status' => 'completed', 'transcript' => $transcript, 'error' => null];
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
}
