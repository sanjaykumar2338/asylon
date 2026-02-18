<?php

namespace App\Jobs;

use App\Models\Report;
use App\Services\ThreatAssessmentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeThreatAssessment implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public string $reportId)
    {
    }

    public function handle(ThreatAssessmentService $service): void
    {
        $report = Report::query()
            ->with(['files', 'riskAnalysis', 'threatAssessment'])
            ->find($this->reportId);

        if (! $report) {
            return;
        }

        try {
            $service->assess($report);
        } catch (\Throwable $e) {
            Log::error('Threat assessment failed.', [
                'report_id' => $this->reportId,
                'exception' => $e,
            ]);
        }
    }
}
