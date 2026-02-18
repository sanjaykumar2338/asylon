<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected string $code)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('Your verification code'))
            ->greeting(__('Verify your login'))
            ->line(__('Use the code below to finish signing in:'))
            ->line('# '.$this->code)
            ->line(__('This code expires in 10 minutes. If you did not try to sign in, you can ignore this email.'));
    }
}
