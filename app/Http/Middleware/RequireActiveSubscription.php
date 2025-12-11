<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->hasRole('platform_admin')) {
            return $next($request);
        }

        $org = $user->org;

        if (! $org || $org->billing_status !== 'active') {
            return redirect()
                ->route('billing.choose_plan')
                ->with('error', __('Your organization does not have an active subscription. Please choose a plan to continue.'));
        }

        return $next($request);
    }
}
