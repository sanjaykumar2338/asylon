<?php

namespace App\Http\Middleware;

use App\Models\Org;
use App\Models\Report;
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
        $org = $this->resolveOrg($request);

        if (! $this->isEnabled($org)) {
            return $next($request);
        }

        $originalIp = $request->ip();
        $userAgent = $request->userAgent();

        $subpoenaHash = $this->fingerprint($originalIp, $userAgent);

        $request->attributes->set('privacy.ultra_private', true);
        $request->attributes->set('privacy.subpoena_hash', $subpoenaHash);
        $request->attributes->set('privacy.org_id', $org?->id);

        $this->scrubRequest($request);

        /** @var Response $response */
        $response = $next($request);

        // Placeholder: If tracking cookies/analytics ever added, drop them here.
        // Currently there are none; we intentionally leave the session/XSRF cookies intact for form security.
        return $response;
    }

    /**
     * Determine if ultra-private mode should be enforced.
     */
    protected function isEnabled(?Org $org): bool
    {
        return (bool) ($org?->enable_ultra_private_mode ?? config('asylon.ultra_private_mode', false));
    }

    /**
     * Build a non-reversible fingerprint for subpoenas without storing raw IP or UA data.
     */
    protected function fingerprint(?string $ip, ?string $userAgent): ?string
    {
        $ip = $ip ? trim($ip) : '';
        $userAgent = $userAgent ? trim($userAgent) : '';

        if ($ip === '' && $userAgent === '') {
            return null;
        }

        return hash('sha256', implode('|', [$ip, $userAgent, config('app.key')]));
    }

    /**
     * Remove IP/UA/location metadata before it can be logged or persisted.
     */
    protected function scrubRequest(Request $request): void
    {
        $request->server->set('REMOTE_ADDR', null);

        foreach (['HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_CF_CONNECTING_IP', 'HTTP_X_REAL_IP'] as $serverKey) {
            $request->server->set($serverKey, null);
        }

        foreach (['User-Agent', 'X-Forwarded-For', 'CF-IPCountry', 'CF-IPCity', 'CF-IPLatitude', 'CF-IPLongitude', 'X-Real-IP'] as $header) {
            $request->headers->remove($header);
        }

        $request->attributes->set('privacy.scrubbed', true);
    }

    /**
     * Attempt to resolve the organization from the incoming request.
     */
    protected function resolveOrg(Request $request): ?Org
    {
        $routeOrg = $request->route('org');
        if ($routeOrg instanceof Org) {
            return $routeOrg;
        }

        if (is_numeric($routeOrg)) {
            return Org::find((int) $routeOrg);
        }

        $orgCode = $request->route('org_code') ?? $request->input('org_code');
        if (is_string($orgCode) && trim($orgCode) !== '') {
            $org = Org::where('org_code', trim($orgCode))->first();
            if ($org) {
                return $org;
            }
        }

        $token = $request->route('token');
        if (is_string($token) && $token !== '') {
            $report = Report::where('chat_token', $token)->first();
            if ($report?->org_id) {
                return Org::find($report->org_id);
            }
        }

        $reportId = $request->route('id');
        if ($reportId !== null && $reportId !== '') {
            $report = Report::withTrashed()->find($reportId);
            if ($report?->org_id) {
                return Org::find($report->org_id);
            }
        }

        $orgId = $request->input('org_id');
        if ($orgId !== null && $orgId !== '') {
            return Org::find((int) $orgId);
        }

        return null;
    }
}
