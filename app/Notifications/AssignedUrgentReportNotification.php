<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignedUrgentReportNotification extends Notification
{
    public function __construct(protected Report $report)
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
        $report = $this->report->loadMissing('org');
        $orgName = $report->org?->name ?? 'Unassigned organization';
        $categoryLabel = $report->subcategory
            ? "{$report->category} - {$report->subcategory}"
            : $report->category;
        $reportUrl = route('reports.show', $report);
        $submittedAt = $report->created_at
            ? $report->created_at->timezone(config('app.timezone'))->format('M d, Y H:i')
            : 'recently';
        $violationDate = $report->violation_date
            ? $report->violation_date->format('M d, Y')
            : 'Not provided';

        return (new MailMessage())
            ->subject("Assigned urgent report: {$report->category}")
            ->greeting("Hello {$notifiable->name},")
            ->line('You have been assigned as the on-call reviewer for a new urgent report.')
            ->line("Organization: {$orgName}")
            ->line("Category: {$categoryLabel}")
            ->line("Submitted at: {$submittedAt}")
            ->line("Violation date: {$violationDate}")
            ->action('Open report', $reportUrl)
            ->line('Please review and respond promptly.')
            ->line(config('asylon.privacy.email_footer'));
    }
}
