<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class AttachmentSafetyScanner
{
    /**
     * Lightweight nudity/graphic detection heuristic.
     *
     * @return array{status:string,reasons:array<int,string>,sensitive:bool}
     */
    public function evaluate(UploadedFile $file): array
    {
        $name = strtolower((string) $file->getClientOriginalName());
        $mime = strtolower((string) $file->getMimeType());
        $isMedia = $this->isMedia($mime, $name);

        $reasons = [];
        $status = 'clear';
        $sensitive = false;

        if (! $isMedia) {
            return compact('status', 'reasons', 'sensitive');
        }

        $flagKeywords = ['nsfw', 'nude', 'nudity', 'explicit', 'gore', 'violent', 'graphic', 'blood'];
        foreach ($flagKeywords as $keyword) {
            if (Str::contains($name, $keyword)) {
                $reasons[] = "Keyword match: {$keyword}";
            }
        }

        if (! empty($reasons)) {
            $status = 'flagged';
            $sensitive = true;
        } else {
            // Default to pending visual scan for media content.
            $status = 'pending_review';
            $sensitive = true;
            $reasons[] = 'Media file queued for safety review';
        }

        return compact('status', 'reasons', 'sensitive');
    }

    protected function isMedia(string $mime, string $name): bool
    {
        if (Str::startsWith($mime, ['image/', 'video/'])) {
            return true;
        }

        if (Str::contains($mime, ['image', 'video'])) {
            return true;
        }

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tif', 'tiff', 'heic', 'heif', 'mp4', 'mov', 'avi', 'mpeg', 'mpg', 'webm', 'mkv'], true);
    }
}
