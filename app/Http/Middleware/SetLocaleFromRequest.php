<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocaleFromRequest
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $languages = array_keys(config('asylon.languages', []));
        $queryLocale = $request->query('lang');
        $sessionLocale = session('asylon.locale');

        if ($queryLocale && in_array($queryLocale, $languages, true)) {
            app()->setLocale($queryLocale);
            session(['asylon.locale' => $queryLocale]);
        } elseif ($sessionLocale && in_array($sessionLocale, $languages, true)) {
            app()->setLocale($sessionLocale);
        }

        return $next($request);
    }
}
