<?php

namespace App\Notifications;

use App\Models\Report;
use App\Models\NotificationTemplate;
use App\Support\TemplateRenderer;
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
        $report = $this->report->loadMissing('org');
        $locale = $report->org?->default_locale ?: config('app.locale', 'en');
        $orgName = $report->org?->name ?? __('notifications.misc.unknown_org', [], $locale);
        $reportUrl = ReportLinkGenerator::dashboard($report, $this->baseUrl);
        $categoryLabel = $report->subcategory
            ? "{$report->category} - {$report->subcategory}"
            : $report->category;
        $submittedAt = $report->created_at
            ? $report->created_at->timezone(config('app.timezone'))->format('M d, Y H:i')
            : __('notifications.misc.recently', [], $locale);

        $templateType = $report->urgent ? NotificationTemplate::TYPE_URGENT : NotificationTemplate::TYPE_ALERT;
        $template = NotificationTemplate::resolve($report->org_id, NotificationTemplate::CHANNEL_EMAIL, $templateType);

        $templateData = [
            'school_name' => $orgName,
            'category' => $categoryLabel,
            'urgency' => $report->urgent ? 'Urgent' : ucfirst((string) $report->severity),
            'date_time' => $submittedAt,
            'report_link' => $reportUrl,
            'report_id' => $report->id,
        ];

        $subject = TemplateRenderer::render($template['subject'] ?? '', $templateData);
        $body = TemplateRenderer::render($template['body'] ?? '', $templateData);

        return (new MailMessage())
            ->subject($subject)
            ->view('emails.urgent_report', [
                'report' => $report,
                'orgName' => $orgName,
                'categoryLabel' => $categoryLabel,
                'reportUrl' => $reportUrl,
                'locale' => $locale,
                'templateBody' => $body,
                'templateSubject' => $subject,
            ]);
    }
}
