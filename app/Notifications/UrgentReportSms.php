<?php

namespace App\Notifications;

use App\Models\Report;
use App\Notifications\Channels\TwilioChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class UrgentReportSms extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Report $report, protected string $phoneNumber)
    {
        $this->queue = 'notifications';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [TwilioChannel::class];
    }

    /**
     * Build the Twilio payload.
     *
     * @param  mixed  $notifiable
     * @return array<string, string>
     */
    public function toTwilio(mixed $notifiable): array
    {
        $report = $this->report;
        $orgName = $report->org?->name ?? 'Unknown org';
        $categoryLabel = $report->subcategory
            ? "{$report->category} - {$report->subcategory}"
            : $report->category;

        return [
            'to' => $this->phoneNumber,
            'body' => sprintf(
                'Urgent report for %s (%s) at %s. Review ASAP.',
                $orgName,
                $categoryLabel,
                $report->created_at?->format('M d H:i')
            ),
        ];
    }
}
