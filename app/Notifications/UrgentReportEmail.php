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
        $submittedAt = $report->created_at?->timezone(config('app.timezone'))->format('M d, Y H:i');
        $violationDate = $report->violation_date?->format('M d, Y') ?? 'Not provided';

        return (new MailMessage())
            ->subject("URGENT: New {$report->category} report")
            ->greeting('Attention required')
            ->line("Organization: {$orgName}")
            ->line("Category: {$categoryLabel}")
            ->line("Submitted at: {$submittedAt}")
            ->line("Violation date: {$violationDate}")
            ->action('Open in dashboard', $reportUrl)
            ->line('Please review and respond as soon as possible.')
            ->line(config('asylon.privacy.email_footer'));
    }
}
