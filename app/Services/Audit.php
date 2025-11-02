<?php

namespace App\Services;

use App\Models\AuditLog;

class Audit
{
    /**
     * Persist an audit log entry.
     *
     * @param  array<string, mixed>  $meta
     */
    public static function log(string $actorType, string $action, string $targetType, mixed $targetId, array $meta = []): void
    {
        $user = auth()->user();

        AuditLog::create([
            'actor_type' => $actorType,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => (string) $targetId,
            'org_id' => $user?->org_id,
            'user_id' => $user?->id,
            'meta' => $meta,
        ]);
    }
}
