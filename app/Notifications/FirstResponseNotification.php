<?php

namespace App\Notifications;

use App\Models\Report;
use App\Support\ReportLinkGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FirstResponseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Report $report,
        protected ?string $baseUrl = null,
        protected ?string $responder = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $report = $this->report->loadMissing('org');
        $locale = $report->org?->default_locale ?: config('app.locale', 'en');
        $orgName = $report->org?->name ?? __('notifications.misc.unknown_org', [], $locale);
        $dashboardUrl = ReportLinkGenerator::dashboard($report, $this->baseUrl);

        return (new MailMessage())
            ->locale($locale)
            ->subject(__('notifications.first_response.subject', ['id' => $report->id], $locale))
            ->greeting(__('notifications.first_response.greeting', ['name' => $notifiable->name ?? __('notifications.misc.unknown_name', [], $locale)], $locale))
            ->line(__('notifications.first_response.body', [], $locale))
            ->line(__('notifications.labels.case_id', [], $locale).': '.$report->id)
            ->line(__('notifications.labels.organization', [], $locale).': '.$orgName)
            ->line(__('notifications.labels.category', [], $locale).': '.$report->category)
            ->line(__('notifications.labels.responder', ['name' => $this->responder ?? __('notifications.misc.unknown_name', [], $locale)], $locale))
            ->action(__('notifications.actions.open_dashboard', [], $locale), $dashboardUrl)
            ->line(__('notifications.first_response.thanks', [], $locale));
    }
}
