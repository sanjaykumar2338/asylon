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
        $locale = $report->org?->default_locale ?: config('app.locale', 'en');
        $orgName = $report->org?->name ?? __('notifications.misc.unknown_org', [], $locale);
        $categoryLabel = $report->subcategory
            ? "{$report->category} - {$report->subcategory}"
            : $report->category;
        $dashboardUrl = ReportLinkGenerator::dashboard($report, $this->baseUrl);
        $followupUrl = ReportLinkGenerator::followup($report, $this->baseUrl);
        $submitted = $report->created_at
            ? $report->created_at->timezone(config('app.timezone'))->format('M d, Y H:i')
            : __('notifications.misc.recently', [], $locale);
        $messageBody = Str::of((string) $this->message->message)
            ->trim()
            ->limit(800, '...');

        return (new MailMessage())
            ->locale($locale)
            ->subject(__('notifications.reporter_followup.subject', [
                'org' => $orgName,
                'category' => $report->category,
            ], $locale))
            ->view('emails.reporter_followup', [
                'report' => $report,
                'submitted' => $submitted,
                'categoryLabel' => $categoryLabel,
                'orgName' => $orgName,
                'dashboardUrl' => $dashboardUrl,
                'followupUrl' => $followupUrl,
                'messageBody' => $messageBody,
                'locale' => $locale,
            ]);
    }
}
