<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Twilio\Rest\Client;

class TwilioChannel
{
    public function __construct(private Client $client)
    {
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     */
    public function send($notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toTwilio')) {
            return;
        }

        $message = $notification->toTwilio($notifiable);

        if (! is_array($message)) {
            return;
        }

        $to = $message['to'] ?? ($notifiable->routeNotificationFor('twilio') ?? null);
        $body = $message['body'] ?? null;

        $config = config('services.twilio');

        if (
            ! $to ||
            ! $body ||
            empty($config['from']) ||
            empty($config['sid']) ||
            empty($config['token'])
        ) {
            return;
        }

        $this->client->messages->create($to, [
            'from' => $config['from'],
            'body' => $body,
        ]);
    }
}
