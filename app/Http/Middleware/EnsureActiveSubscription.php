<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
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

        if ($this->isAllowlisted($request)) {
            return $next($request);
        }

        $org = $user->org;

        if (! $org || $org->billing_status !== 'active') {
            return redirect()
                ->route('billing.choose_plan')
                ->with('warning', __('Your organization needs an active subscription to continue. Please choose a plan.'));
        }

        return $next($request);
    }

    protected function isAllowlisted(Request $request): bool
    {
        $routeName = $request->route()?->getName();

        if ($routeName && Str::is([
            'billing.*',
            'logout',
            'profile.*',
            'support',
        ], $routeName)) {
            return true;
        }

        return $request->is('billing/*')
            || $request->is('logout')
            || $request->is('profile')
            || $request->is('profile/*')
            || $request->is('support');
    }
}
