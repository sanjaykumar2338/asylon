<?php

namespace App\Services;

use App\Models\OrgAlertContact;
use App\Models\Report;
use App\Models\ReportChatMessage;
use App\Notifications\ReporterFollowupEmail;
use App\Services\Sms\TelnyxSmsService;
use App\Support\ReportLinkGenerator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Throwable;

class ReporterFollowupNotifier
{
    public function __construct(protected TelnyxSmsService $sms)
    {
    }

    /**
     * Send email and SMS alerts to alert contacts when a reporter posts a message.
     */
    public function notify(Report $report, ReportChatMessage $message, ?string $baseUrl = null): void
    {
        $report->loadMissing('org');

        $contacts = OrgAlertContact::query()
            ->where('org_id', $report->org_id)
            ->where('is_active', true)
            ->get();

        if ($contacts->isEmpty()) {
            Log::info('Reporter follow-up skipped; no alert contacts found.', [
                'report_id' => $report->getKey(),
                'org_id' => $report->org_id,
            ]);

            return;
        }

        $emailCount = 0;
        $smsCount = 0;
        $emailFailures = [];
        $smsFailures = [];
        $smsMessage = $this->buildSmsMessage($report, $message, $baseUrl);

        foreach ($contacts as $contact) {
            $value = trim((string) $contact->value);

            if ($value === '') {
                continue;
            }

            if ($contact->type === 'email') {
                try {
                    Notification::route('mail', $value)
                        ->notify(new ReporterFollowupEmail($report, $message, $baseUrl));
                    $emailCount++;
                } catch (Throwable $e) {
                    $emailFailures[] = $value;
                    Log::error('Reporter follow-up email failed to send.', [
                        'report_id' => $report->getKey(),
                        'contact' => $value,
                        'exception' => $e,
                    ]);
                }

                continue;
            }

            if ($contact->type === 'sms') {
                $result = $this->sms->send($value, $smsMessage);

                if (isset($result['ok'])) {
                    $smsCount++;
                    continue;
                }

                if (($result['skipped'] ?? false) === true) {
                    continue;
                }

                $smsFailures[] = [
                    'phone' => $value,
                    'reason' => $result['reason'] ?? $result['error'] ?? 'unknown',
                ];
            }
        }

        Audit::log('system', 'reporter_followup_alert', 'report', $report->getKey(), [
            'emails_sent' => $emailCount,
            'email_failures' => $emailFailures,
            'sms_sent' => $smsCount,
            'sms_failures' => $smsFailures,
        ]);
    }

    /**
     * Build a concise SMS payload summarizing the reporter message.
     */
    protected function buildSmsMessage(Report $report, ReportChatMessage $message, ?string $baseUrl = null): string
    {
        $orgName = $report->org?->name ?? 'Org';
        $category = $report->subcategory
            ? "{$report->category} / {$report->subcategory}"
            : $report->category;

        $snippet = Str::of((string) $message->message)
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->limit(100, '...');

        $dashboardUrl = ReportLinkGenerator::dashboard($report, $baseUrl);

        return sprintf(
            'Reporter update for %s (%s): "%s" Review: %s',
            Str::limit($orgName, 32, ''),
            Str::limit($category, 40, ''),
            $snippet,
            $dashboardUrl
        );
    }
}
