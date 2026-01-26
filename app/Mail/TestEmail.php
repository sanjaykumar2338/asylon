<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function build()
    {
        return $this->subject(__('Asylon SMTP Test'))
            ->view('emails.test-email')
            ->with([
                'timestamp' => now()->toDayDateTimeString(),
                'appName' => config('app.name', 'Asylon'),
            ]);
    }
}
