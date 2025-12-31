<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlatformAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || (! $user->isPlatformAdmin() && ! $user->isSuperAdmin())) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
