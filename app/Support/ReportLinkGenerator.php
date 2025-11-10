<?php

namespace App\Support;

use App\Models\Report;

class ReportLinkGenerator
{
    /**
     * Build an absolute dashboard URL to a report.
     */
    public static function dashboard(Report $report, ?string $baseUrl = null): string
    {
        $base = static::normalizeBaseUrl($baseUrl);
        $path = route('reports.show', $report, absolute: false);

        return static::concat($base, $path);
    }

    /**
     * Ensure we always have a usable base URL.
     */
    protected static function normalizeBaseUrl(?string $baseUrl): string
    {
        $value = trim((string) ($baseUrl ?? ''));

        if ($value === '') {
            // MODIFICATION:
            // Instead of getting the static config('app.url'),
            // we dynamically get the root of the *current* request.
            //
            // This will return "http://example.com" or "https://sub.domain.org"
            // based on how the user is accessing the site.
            //
            // In a CLI (command-line) context, this helper will
            // automatically fall back to using your config('app.url'),
            // so it remains safe for Artisan commands.
            $value = (string) request()->root();
        }

        $value = (string) request()->root();
        // We still use rtrim just in case the provided $baseUrl
        // (if not empty) has a trailing slash.
        return rtrim($value, '/');
    }

    protected static function concat(string $base, string $path): string
    {
        $relative = '/'.ltrim($path, '/');

        return $base.$relative;
    }
}
