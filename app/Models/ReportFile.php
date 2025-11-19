<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ReportFile extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'report_id',
        'path',
        'anonymized_path',
        'original_name',
        'mime',
        'size',
        'comment',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'size' => 'integer',
    ];

    /**
     * Report the file belongs to.
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Determine whether the file is an audio recording.
     */
    public function isAudio(): bool
    {
        $mime = strtolower((string) ($this->mime ?? ''));

        if ($this->looksLikeVoiceRecording()) {
            return true;
        }

        if ($mime !== '' && (Str::startsWith($mime, 'audio/') || Str::contains($mime, 'audio'))) {
            return true;
        }

        $candidate = $this->original_name ?: $this->path ?: '';
        $extension = strtolower((string) pathinfo($candidate, PATHINFO_EXTENSION));

        if ($extension === '' && $this->path) {
            $extension = strtolower((string) pathinfo($this->path, PATHINFO_EXTENSION));
        }

        if ($extension === '') {
            return false;
        }

        $audioExtensions = ['mp3', 'wav', 'aac', 'm4a', 'ogg', 'opus', 'weba', 'flac'];

        return in_array($extension, $audioExtensions, true);
    }

    /**
     * Detect MediaRecorder-sourced uploads that need anonymization even if labeled video/webm.
     */
    protected function looksLikeVoiceRecording(): bool
    {
        $original = strtolower((string) ($this->original_name ?? ''));

        return $original !== '' && Str::startsWith($original, 'voice-report-');
    }
}
