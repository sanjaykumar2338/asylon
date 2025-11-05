<?php

namespace App\Notifications;

use App\Models\Report;
use App\Notifications\Channels\TwilioChannel;
use Illuminate\Notifications\Notification;

class UrgentReportSms extends Notification
{
    public function __construct(protected Report $report, protected string $phoneNumber)
    {
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
        $reportUrl = route('reports.show', $report);
        $submittedAt = $report->created_at?->format('M d H:i');

        return [
            'to' => $this->phoneNumber,
            'body' => sprintf(
                'URGENT %s at %s (%s). Review: %s',
                $report->category,
                $orgName,
                $submittedAt ?? 'recent',
                $reportUrl
            ),
        ];
    }
}
