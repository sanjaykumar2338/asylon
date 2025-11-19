<?php

namespace App\Jobs;

use App\Models\ReportFile;
use App\Services\VoiceAnonymizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AnonymizeVoiceJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Cap processing time in case ffmpeg stalls.
     */
    public int $timeout = 120;

    public function __construct(public ReportFile $file)
    {
    }

    public function handle(VoiceAnonymizer $anonymizer): void
    {
        $file = $this->file->fresh();

        if (! $file) {
            return;
        }

        if ($file->anonymized_path) {
            return;
        }

        if (! $this->shouldAnonymize($file)) {
            return;
        }

        $disk = Storage::disk('public');

        if (! $file->path || ! $disk->exists($file->path)) {
            Log::warning('AnonymizeVoiceJob skipped; source file missing.', [
                'file_id' => $file->getKey(),
                'path' => $file->path,
            ]);

            return;
        }

        $inputPath = $disk->path($file->path);
        $outputRelativePath = $this->buildOutputPath($file->path);
        $outputPath = $disk->path($outputRelativePath);

        $result = $anonymizer->anonymize($inputPath, $outputPath);

        if ($result === false) {
            return;
        }

        $file->anonymized_path = $outputRelativePath;
        $file->save();
    }

    protected function shouldAnonymize(ReportFile $file): bool
    {
        $mime = (string) ($file->mime ?? '');
        $extension = strtolower((string) pathinfo((string) $file->original_name, PATHINFO_EXTENSION));
        $audioExtensions = ['mp3', 'wav', 'aac', 'm4a', 'ogg', 'opus', 'weba'];

        return Str::of($mime)->startsWith('audio/')
            || Str::of($mime)->contains('audio')
            || in_array($extension, $audioExtensions, true);
    }

    protected function buildOutputPath(string $path): string
    {
        $info = pathinfo($path);
        $dir = $info['dirname'] ?? '';
        $filename = $info['filename'] ?? 'voice';
        $extension = $info['extension'] ?? 'mp3';

        $prefix = $dir && $dir !== '.' ? $dir.'/' : '';

        return $prefix.$filename.'_anonymized.'.$extension;
    }
}
