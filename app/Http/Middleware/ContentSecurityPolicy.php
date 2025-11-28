<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Apply a baseline Content-Security-Policy header to all responses.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (! $response->headers->has('Content-Security-Policy')) {
            $scriptSources = [
                "'self'",
                "'unsafe-inline'",
                'https://cdn.jsdelivr.net',
            ];

            $styleSources = [
                "'self'",
                "'unsafe-inline'",
                'https://cdn.jsdelivr.net',
                'https://fonts.googleapis.com',
            ];

            $fontSources = [
                "'self'",
                'data:',
                'https://fonts.gstatic.com',
                'https://cdn.jsdelivr.net',
            ];

            $policy = implode('; ', [
                "default-src 'self'",
                "img-src 'self' data: blob:",
                "media-src 'self' data: blob:",
                'script-src '.implode(' ', $scriptSources),
                'style-src '.implode(' ', $styleSources),
                'font-src '.implode(' ', $fontSources),
                "connect-src 'self'",
                "frame-ancestors 'self'",
            ]);

            $response->headers->set('Content-Security-Policy', $policy);
        }

        return $response;
    }
}
