<?php

namespace App\Notifications;

use App\Models\Report;
use App\Support\ReportLinkGenerator;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignedUrgentReportNotification extends Notification
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
        $report = $this->report->loadMissing('org');
        $locale = $report->org?->default_locale ?: config('app.locale', 'en');
        $orgName = $report->org?->name ?? __('notifications.misc.unknown_org', [], $locale);
        $categoryLabel = $report->subcategory
            ? "{$report->category} - {$report->subcategory}"
            : $report->category;
        $reportUrl = ReportLinkGenerator::dashboard($report, $this->baseUrl);
        $submittedAt = $report->created_at
            ? $report->created_at->timezone(config('app.timezone'))->format('M d, Y H:i')
            : __('notifications.misc.recently', [], $locale);
        $violationDate = $report->violation_date
            ? $report->violation_date->format('M d, Y')
            : __('notifications.labels.not_provided', [], $locale);

        return (new MailMessage())
            ->subject(__('notifications.assigned_urgent.subject', ['category' => $report->category], $locale))
            ->greeting(__('notifications.assigned_urgent.greeting', ['name' => $notifiable->name ?? __('notifications.misc.unknown_name', [], $locale)], $locale))
            ->line(__('notifications.assigned_urgent.body', [], $locale))
            ->line(__('notifications.labels.organization', [], $locale).": {$orgName}")
            ->line(__('notifications.labels.category', [], $locale).": {$categoryLabel}")
            ->line(__('notifications.labels.submitted', [], $locale).": {$submittedAt}")
            ->line(__('notifications.labels.violation_date', [], $locale).": {$violationDate}")
            ->action(__('notifications.actions.open_report', [], $locale), $reportUrl)
            ->line(__('notifications.assigned_urgent.review_prompt', [], $locale))
            ->line(__('common.email_footer', [], $locale));
    }
}
