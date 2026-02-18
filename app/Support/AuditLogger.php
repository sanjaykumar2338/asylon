<?php

namespace App\Support;



use App\Models\AuditLog;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuditLogger
{
    /**
     * Record an action tied to a specific case/report.
     *
     * @param  array<string, mixed>  $meta
     */
    public static function caseAction(?User $user, Report $case, string $action, array $meta = []): void
    {
        $orgId = $case->org_id ?? $user?->org_id;

        self::log([
            'org_id' => $orgId,
            'user_id' => $user?->id,
            'case_id' => $case->getKey(),
            'action' => $action,
            'actor_type' => $user?->role,
            'meta' => $meta,
        ], $case->org?->enable_ultra_private_mode ?? false);
    }

    /**
     * Record a generic audit log row.
     *
     * @param  array<string, mixed>  $data
     */
    public static function log(array $data, bool $hashUserAgent = false): void
    {
        try {
            $request = request();
            $ip = $request ? $request->ip() : null;
            $userAgent = $request ? $request->userAgent() : null;

            if ($hashUserAgent && $userAgent) {
                $userAgent = hash('sha256', $userAgent);
            }

            if (config('asylon.ultra_private_mode', false) && $userAgent) {
                $userAgent = hash('sha256', $userAgent);
            }

            AuditLog::create([
                'org_id' => $data['org_id'] ?? null,
                'user_id' => $data['user_id'] ?? null,
                'case_id' => $data['case_id'] ?? null,
                'action' => $data['action'] ?? '',
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'meta' => $data['meta'] ?? null,
                'actor_type' => $data['actor_type'] ?? null,
                'target_type' => $data['target_type'] ?? null,
                'target_id' => $data['target_id'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to write audit log', [
                'error' => $e->getMessage(),
                'action' => $data['action'] ?? null,
            ]);
        }
    }
}
