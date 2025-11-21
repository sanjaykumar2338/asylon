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
        $report = $this->report->loadMissing('org');
        $locale = $report->org?->default_locale ?: config('app.locale', 'en');
        $orgName = $report->org?->name ?? __('notifications.misc.unknown_org', [], $locale);
        $reportUrl = ReportLinkGenerator::dashboard($report, $this->baseUrl);
        $categoryLabel = $report->subcategory
            ? "{$report->category} - {$report->subcategory}"
            : $report->category;

        return (new MailMessage())
            ->locale($locale)
            ->subject(__('notifications.urgent_alert.subject', ['category' => $report->category], $locale))
            ->view('emails.urgent_report', [
                'report' => $report,
                'orgName' => $orgName,
                'categoryLabel' => $categoryLabel,
                'reportUrl' => $reportUrl,
                'locale' => $locale,
            ]);
    }
}
