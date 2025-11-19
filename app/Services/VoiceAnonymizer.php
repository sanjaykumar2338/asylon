<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Throwable;

class VoiceAnonymizer
{
    public function __construct(protected ?string $binary = null)
    {
    }

    /**
     * Apply a basic pitch/tempo shift to obscure the speaker.
     *
     * @return string|false Output path on success, or false on failure.
     */
    public function anonymize(string $inputPath, string $outputPath, float $pitchMultiplier = 0.82): string|false
    {
        if (! is_file($inputPath)) {
            Log::warning('Voice anonymization skipped; input file missing.', [
                'input' => $inputPath,
            ]);

            return false;
        }

        File::ensureDirectoryExists((string) dirname($outputPath));

        $ffmpeg = $this->binary ?? config('asylon.ffmpeg_path', 'ffmpeg');
        $filter = sprintf('asetrate=44100*%F,aresample=44100,atempo=1.05', $pitchMultiplier);

        $process = new Process([
            $ffmpeg,
            '-y',
            '-i',
            $inputPath,
            '-filter:a',
            $filter,
            '-vn',
            $outputPath,
        ]);

        $process->setTimeout(90);

        try {
            $process->run();
        } catch (Throwable $e) {
            Log::error('Voice anonymization failed to start.', [
                'input' => $inputPath,
                'output' => $outputPath,
                'exception' => $e,
            ]);

            return false;
        }

        if (! $process->isSuccessful() || ! is_file($outputPath)) {
            Log::error('Voice anonymization failed.', [
                'input' => $inputPath,
                'output' => $outputPath,
                'exit_code' => $process->getExitCode(),
                'error' => $process->getErrorOutput(),
            ]);

            return false;
        }

        return $outputPath;
    }
}
