<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UltraPrivateMode
{
    /**
     * When enabled, scrub request metadata for public reporting pages.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('asylon.ultra_private_mode', false)) {
            return $next($request);
        }

        // Basic scrubbing hooks (no IP/UA/device details captured).
        $request->server->set('REMOTE_ADDR', null);
        $request->headers->remove('User-Agent');
        $request->attributes->set('privacy.ultra_private', true);

        /** @var Response $response */
        $response = $next($request);

        // Placeholder: If tracking cookies/analytics ever added, drop them here.
        // Currently there are none; we intentionally leave the session/XSRF cookies intact for form security.
        return $response;
    }
}
