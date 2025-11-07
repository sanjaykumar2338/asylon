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

        if (! $report->urgent) {
            return;
        }

        $report->loadMissing(['org.onCallReviewer']);

        $contacts = OrgAlertContact::query()
            ->where('org_id', $report->org_id)
            ->where('is_active', true)
            ->get();

        $emailCount = 0;
        $smsTargets = collect();
        $onCallNotified = false;
        $emailFailures = [];

        foreach ($contacts as $contact) {
            if ($contact->type === 'email') {
                try {
                    Notification::route('mail', $contact->value)
                        ->notify(new UrgentReportEmail($report));
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
            SendUrgentSmsAlerts::dispatch($report);
            Log::info('Queued urgent SMS job for report.', [
                'report_id' => $report->getKey(),
                'org_id' => $report->org_id,
                'recipients' => $smsTargets->unique()->values(),
            ]);
        }

        $onCallReviewer = $report->org?->onCallReviewer;
        if ($onCallReviewer && $onCallReviewer->active) {
            try {
                $onCallReviewer->notify(new AssignedUrgentReportNotification($report));
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
            ]
        );
    }
}
