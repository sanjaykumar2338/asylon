<?php

namespace App\Listeners;

use App\Events\ReportSubmitted;
use App\Jobs\SendUrgentSmsAlerts;
use App\Models\OrgAlertContact;
use App\Notifications\AssignedUrgentReportNotification;
use App\Notifications\UrgentReportEmail;
use App\Services\Audit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class SendUrgentAlerts implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Maximum seconds the listener may run before failing.
     */
    public int $timeout = 120;

    /**
     * Number of times the listener should be retried.
     */
    public int $tries = 3;

    /**
     * Handle the event.
     */
    public function handle(ReportSubmitted $event): void
    {
        $report = $event->report;
        $baseUrl = $event->baseUrl;

        $report->loadMissing(['org.onCallReviewer']);

        $contacts = $this->determineRecipients($report);

        if ($contacts->isEmpty()) {
            Log::info('No alert contacts matched for report.', [
                'report_id' => $report->getKey(),
                'org_id' => $report->org_id,
                'type' => $report->type,
            ]);
            return;
        }

        $emailCount = 0;
        $smsTargets = collect();
        $onCallNotified = false;
        $emailFailures = [];

        foreach ($contacts as $contact) {
            if ($contact->type === 'email') {
                try {
                    Notification::route('mail', $contact->value)
                        ->notify(new UrgentReportEmail($report, $baseUrl));
                    $emailCount++;
                    Log::info('Urgent report email notification sent.', [
                        'report_id' => $report->getKey(),
                        'contact' => $contact->value,
                        'org_id' => $report->org_id,
                    ]);
                } catch (Throwable $e) {
                    $emailFailures[] = $contact->value;
                    Log::error('Failed to send urgent report email notification.', [
                        'report_id' => $report->getKey(),
                        'contact' => $contact->value,
                        'org_id' => $report->org_id,
                        'exception' => $e,
                    ]);
                }
            } elseif ($contact->type === 'sms') {
                $smsTargets->push($contact->value);
            }
        }

        if ($smsTargets->isNotEmpty()) {
            SendUrgentSmsAlerts::dispatch($report, $baseUrl);
            Log::info('Queued SMS job for report.', [
                'report_id' => $report->getKey(),
                'org_id' => $report->org_id,
                'recipients' => $smsTargets->unique()->values(),
            ]);
        }

        $onCallReviewer = $report->org?->onCallReviewer;
        if ($onCallReviewer && $onCallReviewer->active) {
            try {
                $onCallReviewer->notify(new AssignedUrgentReportNotification($report, $baseUrl));
                $onCallNotified = true;
                Log::info('Urgent report assignment email sent to on-call reviewer.', [
                    'report_id' => $report->getKey(),
                    'on_call_user_id' => $onCallReviewer->getKey(),
                    'org_id' => $report->org_id,
                ]);
            } catch (Throwable $e) {
                Log::error('Failed to notify on-call reviewer about an urgent report.', [
                    'report_id' => $report->getKey(),
                    'on_call_user_id' => $onCallReviewer->getKey(),
                    'org_id' => $report->org_id,
                    'exception' => $e,
                ]);
            }
        }

        Audit::log(
            'system',
            'alert_dispatched',
            'report',
            $report->getKey(),
            [
                'emails' => $emailCount,
                'sms_job_dispatched' => $smsTargets->isNotEmpty(),
                'sms_recipient_count' => $smsTargets->unique()->count(),
                'on_call_notified' => $onCallNotified,
                'email_failures' => $emailFailures,
                'recipients_sent' => $contacts->pluck('value'),
            ]
        );
    }

    /**
     * Determine which alert contacts should receive the report.
     */
    protected function determineRecipients($report)
    {
        $query = OrgAlertContact::query()
            ->where('org_id', $report->org_id)
            ->where('is_active', true);

        if (in_array($report->type, ['hr', 'commendation'], true)) {
            $ids = collect($report->meta['recipients'] ?? [])->filter();

            if ($ids->isEmpty()) {
                return collect();
            }

            $query->whereIn('id', $ids);
        } else {
            $departments = config('asylon.alerts.student_departments', []);
            if (! empty($departments)) {
                $query->whereIn('department', $departments);
            }
        }

        return $query->get();
    }
}
