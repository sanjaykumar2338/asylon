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
            $value = static::hostFromRequest();
        }

        return rtrim($value, '/');
    }

    protected static function concat(string $base, string $path): string
    {
        $relative = '/'.ltrim($path, '/');

        return $base.$relative;
    }

    protected static function hostFromRequest(): string
    {
        $request = null;

        try {
            $request = request();
        } catch (\Throwable) {
            // No request bound (e.g., queue context); fall back to server vars.
        }

        $scheme = 'http';
        $host = null;

        if ($request) {
            $host = $request->getHttpHost() ?: $request->getHost();
            $scheme = $request->getScheme() ?: $scheme;
        }

        if ($host === null || $host === '') {
            $host = $_SERVER['HTTP_HOST']
                ?? $_SERVER['SERVER_NAME']
                ?? 'localhost';

            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
                $scheme = 'https';
            }
        }

        return sprintf('%s://%s', $scheme, ltrim($host, '/'));
    }
}
