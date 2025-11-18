<?php

namespace App\Notifications;

use App\Models\Report;
use App\Support\ReportLinkGenerator;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UrgentReportEmail extends Notification
{
    public function __construct(protected Report $report, protected ?string $baseUrl = null)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $report = $this->report;
        $orgName = $report->org?->name ?? 'Unassigned organization';
        $reportUrl = ReportLinkGenerator::dashboard($report, $this->baseUrl);
        $categoryLabel = $report->subcategory
            ? "{$report->category} - {$report->subcategory}"
            : $report->category;

        return (new MailMessage())
            ->subject("URGENT: New {$report->category} report")
            ->view('emails.urgent_report', [
                'report' => $report,
                'orgName' => $orgName,
                'categoryLabel' => $categoryLabel,
                'reportUrl' => $reportUrl,
            ]);
    }
}
