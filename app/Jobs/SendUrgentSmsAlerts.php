<?php

namespace App\Jobs;

use App\Models\OrgAlertContact;
use App\Models\Report;
use App\Services\Audit;
use App\Services\Sms\TelnyxSmsService;
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
        $statusSnippet = strtoupper($report->status ?? 'open');
        $reportUrl = ReportLinkGenerator::dashboard($report, $this->baseUrl);
        $submittedAt = optional($report->created_at)->format('M d H:i');

        return sprintf(
            'URGENT %s at %s (%s). Review: %s',
            Str::limit($category, 70),
            Str::limit($orgName, 30),
            $submittedAt ?? 'recent',
            $reportUrl
        );
    }
}
