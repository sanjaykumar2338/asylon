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
     * Build a public follow-up portal URL for the report.
     */
    public static function followup(Report $report, ?string $baseUrl = null): string
    {
        $base = static::normalizeBaseUrl($baseUrl);
        $token = (string) $report->chat_token;

        if ($token === '') {
            return static::dashboard($report, $base);
        }

        $path = route('followup.show', ['token' => $token], absolute: false);

        return static::concat($base, $path);
    }

    /**
     * Ensure we always have a usable base URL.
     */
    protected static function normalizeBaseUrl(?string $baseUrl): string
    {
        $value = trim((string) ($baseUrl ?? ''));

        if ($value === '') {
            $value = (string) config('app.url', 'http://localhost');
        }

        return rtrim($value, '/');
    }

    protected static function concat(string $base, string $path): string
    {
        $relative = '/'.ltrim($path, '/');

        return $base.$relative;
    }

}
