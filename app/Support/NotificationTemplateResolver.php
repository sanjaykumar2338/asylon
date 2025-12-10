<?php

namespace App\Support;

use App\Models\NotificationTemplate;
use App\Models\Org;

class NotificationTemplateResolver
{
    /**
     * Resolve a template with org override then global fallback.
     *
     * @return array{subject:?string,body:string,source:string,channel:string,type:string}
     */
    public static function resolve(string $channel, string $type, ?Org $org = null): array
    {
        return NotificationTemplate::resolve($org?->id, $channel, $type);
    }
}
