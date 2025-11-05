<?php

namespace App\Listeners;

use App\Events\ReportSubmitted;
use App\Models\OrgAlertContact;
use App\Notifications\Channels\TwilioChannel;
use App\Notifications\AssignedUrgentReportNotification;
use App\Notifications\UrgentReportEmail;
use App\Notifications\UrgentReportSms;
use App\Services\Audit;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class SendUrgentAlerts
{
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
        $smsCount = 0;
        $onCallNotified = false;
        $emailFailures = [];
        $smsFailures = [];

        foreach ($contacts as $contact) {
            if ($contact->type === 'email') {
                try {
                    Notification::route('mail', $contact->value)
                        ->notify(new UrgentReportEmail($report));
                    $emailCount++;
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
                try {
                    Notification::route(TwilioChannel::class, $contact->value)
                        ->notify(new UrgentReportSms($report, $contact->value));
                    $smsCount++;
                } catch (Throwable $e) {
                    $smsFailures[] = $contact->value;
                    Log::error('Failed to send urgent report SMS notification.', [
                        'report_id' => $report->getKey(),
                        'contact' => $contact->value,
                        'org_id' => $report->org_id,
                        'exception' => $e,
                    ]);
                }
            }
        }

        $onCallReviewer = $report->org?->onCallReviewer;
        if ($onCallReviewer && $onCallReviewer->active) {
            try {
                $onCallReviewer->notify(new AssignedUrgentReportNotification($report));
                $onCallNotified = true;
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
                'sms' => $smsCount,
                'on_call_notified' => $onCallNotified,
                'email_failures' => $emailFailures,
                'sms_failures' => $smsFailures,
            ]
        );
    }
}
