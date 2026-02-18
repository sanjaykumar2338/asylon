<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('org.suspended')) {
            return $next($request);
        }

        if ($request->user()) {
            $org = $request->user()->org;

            if (! $org) {
                abort(403);
            }

            if ($org->billing_status === 'suspended') {
                return redirect()->route('org.suspended');
            }
        }

        return $next($request);
    }
}
