<?php

namespace App\Jobs;

use App\Models\Report;
use App\Services\RiskAnalysisService;
use App\Services\EscalationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AnalyzeReportRisk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Report $report)
    {
    }

    public function handle(RiskAnalysisService $service, EscalationService $escalations): void
    {
        $analysis = $service->analyze($this->report->fresh());
        $escalations->evaluate($this->report->fresh(), $analysis);
    }
}
