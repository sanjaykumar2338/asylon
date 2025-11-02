<?php

namespace App\Listeners;

use App\Events\ReportSubmitted;
use App\Models\OrgAlertContact;
use App\Notifications\Channels\TwilioChannel;
use App\Notifications\UrgentReportEmail;
use App\Notifications\UrgentReportSms;
use App\Services\Audit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendUrgentAlerts implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    /**
     * Handle the event.
     */
    public function handle(ReportSubmitted $event): void
    {
        $report = $event->report;

        if (! $report->urgent) {
            return;
        }

        $contacts = OrgAlertContact::query()
            ->where('org_id', $report->org_id)
            ->where('is_active', true)
            ->get();

        $emailCount = 0;
        $smsCount = 0;

        foreach ($contacts as $contact) {
            if ($contact->type === 'email') {
                Notification::route('mail', $contact->value)
                    ->notify(new UrgentReportEmail($report));
                $emailCount++;
            } elseif ($contact->type === 'sms') {
                Notification::route(TwilioChannel::class, $contact->value)
                    ->notify(new UrgentReportSms($report, $contact->value));
                $smsCount++;
            }
        }

        Audit::log(
            'system',
            'alert_dispatched',
            'report',
            $report->getKey(),
            [
                'emails_sent' => $emailCount,
                'sms_sent' => $smsCount,
            ]
        );
    }
}
