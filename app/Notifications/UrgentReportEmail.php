<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UrgentReportEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Report $report)
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $report = $this->report;
        $orgName = $report->org?->name ?? 'Unassigned organization';
        $reportUrl = route('reports.show', $report);
        $categoryLabel = $report->subcategory
            ? "{$report->category} - {$report->subcategory}"
            : $report->category;

        return (new MailMessage())
            ->subject("Urgent report for {$orgName}")
            ->greeting('Attention required')
            ->line("An urgent report was submitted for {$orgName}.")
            ->line("Category: {$categoryLabel}")
            ->line('Submitted at: '.$report->created_at?->format('M d, Y H:i'))
            ->action('View report', $reportUrl)
            ->line('Please log in to review and respond as soon as possible.');
    }
}
