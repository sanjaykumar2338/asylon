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
        $orgName = $report->org?->name ?? __('Unknown organization');
        $dashboardUrl = ReportLinkGenerator::dashboard($report, $this->baseUrl);

        return (new MailMessage())
            ->subject(__('First response sent for case #:id', ['id' => $report->id]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name ?? __('Admin')]))
            ->line(__('A reviewer sent the first reply to the reporter.'))
            ->line(__('Case: #:id', ['id' => $report->id]))
            ->line(__('Organization: :org', ['org' => $orgName]))
            ->line(__('Category: :category', ['category' => $report->category]))
            ->line(__('Responder: :name', ['name' => $this->responder ?? __('Unknown')]))
            ->action(__('Open in dashboard'), $dashboardUrl)
            ->line(__('Thank you for keeping response times fast.'));
    }
}
