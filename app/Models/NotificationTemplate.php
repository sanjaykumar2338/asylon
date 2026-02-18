<?php

namespace App\Models;

use App\Support\TemplateRenderer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'org_id',
        'channel',
        'type',
        'subject',
        'body',
    ];

    public const CHANNEL_SMS = 'sms';
    public const CHANNEL_EMAIL = 'email';

    public const TYPE_ALERT = 'alert';
    public const TYPE_FOLLOWUP = 'followup';
    public const TYPE_URGENT = 'urgent_alert';

    /**
     * Resolve a template for the given org/channel/type.
     *
     * @return array{subject:?string,body:string,source:string,channel:string,type:string}
     */
    public static function resolve(?int $orgId, string $channel, string $type): array
    {
        $channel = strtolower($channel);
        $type = strtolower($type);

        $defaults = static::defaultTemplate($channel, $type);
        $record = static::query()
            ->where('channel', $channel)
            ->where('type', $type)
            ->when($orgId !== null, fn ($query) => $query->where('org_id', $orgId))
            ->first();

        $source = 'default';

        if (! $record) {
            $record = static::query()
                ->whereNull('org_id')
                ->where('channel', $channel)
                ->where('type', $type)
                ->first();
            $source = $record ? 'global' : 'default';
        } else {
            $source = $record->org_id ? 'org' : 'global';
        }

        $subject = $record?->subject ?? $defaults['subject'] ?? null;
        $body = $record?->body ?? $defaults['body'] ?? '';

        if ($channel === static::CHANNEL_SMS) {
            $body = TemplateRenderer::ensureSmsCompliance($body);
        }

        return [
            'subject' => $subject,
            'body' => $body,
            'source' => $source,
            'channel' => $channel,
            'type' => $type,
        ];
    }

    /**
     * Persist or remove an org-specific template.
     */
    public static function saveTemplate(?int $orgId, string $channel, string $type, ?string $subject, ?string $body): void
    {
        $channel = strtolower($channel);
        $type = strtolower($type);
        $subject = $subject !== null ? trim($subject) : null;
        $body = $body !== null ? trim($body) : null;

        $shouldDelete = ($body === null || $body === '') && ($subject === null || $subject === '');

        if ($shouldDelete) {
            static::query()
                ->where('channel', $channel)
                ->where('type', $type)
                ->where('org_id', $orgId)
                ->delete();

            return;
        }

        if ($channel === static::CHANNEL_SMS && $body !== null && $body !== '') {
            $body = TemplateRenderer::ensureSmsCompliance($body);
        }

        static::updateOrCreate(
            [
                'channel' => $channel,
                'type' => $type,
                'org_id' => $orgId,
            ],
            [
                'subject' => $subject,
                'body' => $body ?? '',
            ]
        );
    }

    /**
     * Default template configuration.
     *
     * @return array<string, mixed>
     */
    public static function definitions(): array
    {
        return [
            'sms' => [
                'alert' => [
                    'label' => 'Alert SMS',
                    'placeholders' => ['school_name', 'category', 'urgency', 'date_time', 'report_link', 'report_id'],
                ],
                'urgent_alert' => [
                    'label' => 'Urgent Alert SMS',
                    'placeholders' => ['school_name', 'category', 'urgency', 'date_time', 'report_link', 'report_id'],
                ],
                'followup' => [
                    'label' => 'Follow-up SMS',
                    'placeholders' => ['school_name', 'category', 'urgency', 'date_time', 'report_link', 'report_id', 'message'],
                ],
            ],
            'email' => [
                'alert' => [
                    'label' => 'Alert Email',
                    'placeholders' => ['school_name', 'category', 'urgency', 'date_time', 'report_link', 'report_id'],
                ],
                'urgent_alert' => [
                    'label' => 'Urgent Alert Email',
                    'placeholders' => ['school_name', 'category', 'urgency', 'date_time', 'report_link', 'report_id'],
                ],
                'followup' => [
                    'label' => 'Follow-up Email',
                    'placeholders' => ['school_name', 'category', 'urgency', 'date_time', 'report_link', 'report_id', 'message'],
                ],
            ],
        ];
    }

    /**
     * Fetch the built-in default template for a channel/type.
     *
     * @return array{subject:?string,body:string}
     */
    public static function defaultTemplate(string $channel, string $type): array
    {
        $defaults = config('notification_templates.defaults', []);
        $channel = strtolower($channel);
        $type = strtolower($type);

        if ($channel === static::CHANNEL_SMS) {
            return [
                'subject' => null,
                'body' => $defaults['sms'][$type] ?? '',
            ];
        }

        return [
            'subject' => $defaults['email'][$type]['subject'] ?? null,
            'body' => $defaults['email'][$type]['body'] ?? '',
        ];
    }

    /**
     * Compliance line required for SMS templates.
     */
    public static function smsComplianceLine(): string
    {
        return config('notification_templates.compliance_sms_line');
    }
}
