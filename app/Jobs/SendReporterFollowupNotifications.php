<?php

namespace App\Jobs;

use App\Models\Report;
use App\Models\ReportChatMessage;
use App\Services\ReporterFollowupNotifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendReporterFollowupNotifications implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Cap the job runtime so it cannot exceed PHP's 30s web limit.
     */
    public int $timeout = 25;

    public function __construct(
        public Report $report,
        public ReportChatMessage $message,
        public ?string $baseUrl = null,
    ) {
    }

    public function handle(ReporterFollowupNotifier $notifier): void
    {
        $report = $this->report->fresh();
        $message = $this->message->fresh();

        if (! $report || ! $message) {
            Log::warning('SendReporterFollowupNotifications skipped; report or message missing.', [
                'report_id' => $this->report->getKey(),
                'message_id' => $this->message->getKey(),
            ]);

            return;
        }

        $notifier->notify($report, $message, $this->baseUrl);
    }
}
