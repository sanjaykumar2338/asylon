<?php

namespace App\Notifications;

use App\Models\Report;
use App\Models\ReportChatMessage;
use App\Support\ReportLinkGenerator;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ReporterFollowupEmail extends Notification
{
    public function __construct(
        protected Report $report,
        protected ReportChatMessage $message,
        protected ?string $baseUrl = null
    ) {
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
        $orgName = $report->org?->name ?? __('Unknown organization');
        $categoryLabel = $report->subcategory
            ? "{$report->category} - {$report->subcategory}"
            : $report->category;
        $dashboardUrl = ReportLinkGenerator::dashboard($report, $this->baseUrl);
        $followupUrl = ReportLinkGenerator::followup($report, $this->baseUrl);
        $submitted = $report->created_at
            ? $report->created_at->timezone(config('app.timezone'))->format('M d, Y H:i')
            : __('recently');
        $messageBody = Str::of((string) $this->message->message)
            ->trim()
            ->limit(800, '...');

        return (new MailMessage())
            ->subject(__('Reporter follow-up for :org (:category)', [
                'org' => $orgName,
                'category' => $report->category,
            ]))
            ->greeting(__('Hello,'))
            ->line(__('A reporter posted a new follow-up message on case :id.', ['id' => $report->id]))
            ->line(__('Organization: :org', ['org' => $orgName]))
            ->line(__('Category: :category', ['category' => $categoryLabel]))
            ->line(__('Submitted: :submitted', ['submitted' => $submitted]))
            ->line(__('Message: ":message"', ['message' => $messageBody]))
            ->action(__('Open report in dashboard'), $dashboardUrl)
            ->line(__('Public follow-up portal: :url', ['url' => $followupUrl]))
            ->line(__('Please log any responses directly in the dashboard to keep the case history complete.'))
            ->line(config('asylon.privacy.email_footer'));
    }
}
