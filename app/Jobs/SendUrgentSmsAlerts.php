<?php

namespace App\Jobs;

use App\Models\OrgAlertContact;
use App\Models\Report;
use App\Services\Audit;
use App\Services\Sms\TelnyxSmsService;
use App\Models\NotificationTemplate;
use App\Support\TemplateRenderer;
use App\Support\ReportLinkGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SendUrgentSmsAlerts implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public Report $report, public ?string $baseUrl = null)
    {
    }

    public function handle(TelnyxSmsService $sms): void
    {
        $report = $this->report->fresh(['org']);

        if (! $report || ! $report->org) {
            Log::warning('SendUrgentSmsAlerts skipped; report or org missing.', [
                'report_id' => $this->report->getKey(),
            ]);

            return;
        }

        $contacts = OrgAlertContact::query()
            ->where('org_id', $report->org_id)
            ->where('is_active', true)
            ->where('type', 'sms')
            ->pluck('value')
            ->filter()
            ->unique()
            ->values();

        if ($contacts->isEmpty()) {
            Log::info('SendUrgentSmsAlerts skipped; no SMS contacts configured.', [
                'report_id' => $report->getKey(),
                'org_id' => $report->org_id,
            ]);

            return;
        }

        $message = $this->buildMessage($report);
        $success = 0;
        $failed = 0;
        $skipped = 0;
        $failures = [];

        foreach ($contacts as $phone) {
            $result = $sms->send($phone, $message);

            if (isset($result['ok'])) {
                $success++;
                continue;
            }

            if (($result['skipped'] ?? false) === true) {
                $skipped++;
                continue;
            }

            $failed++;
            $failures[] = [
                'phone' => $phone,
                'reason' => $result['error'] ?? $result['reason'] ?? 'unknown',
            ];
        }

        Audit::log('system', 'alert_dispatched', 'report', $report->getKey(), [
            'channel' => 'sms',
            'org_id' => $report->org_id,
            'success' => $success,
            'failed' => $failed,
            'skipped' => $skipped,
            'failures' => $failures,
        ]);
    }

    protected function buildMessage(Report $report): string
    {
        $orgName = $report->org?->name ?? 'Unknown org';
        $category = $report->subcategory
            ? "{$report->category} / {$report->subcategory}"
            : $report->category;

        $reportUrl = ReportLinkGenerator::dashboard($report, $this->baseUrl);
        $submittedAt = optional($report->created_at)?->format('M d H:i') ?? 'recent';

        $templateType = $report->urgent ? NotificationTemplate::TYPE_URGENT : NotificationTemplate::TYPE_ALERT;
        $template = NotificationTemplate::resolve($report->org_id, NotificationTemplate::CHANNEL_SMS, $templateType);

        return TemplateRenderer::render($template['body'], [
            'school_name' => Str::limit($orgName, 64, ''),
            'category' => Str::limit($category, 64, ''),
            'urgency' => $report->urgent ? 'Urgent' : ucfirst((string) $report->severity),
            'date_time' => $submittedAt,
            'report_link' => $reportUrl,
            'report_id' => $report->id,
        ]);
    }
}
