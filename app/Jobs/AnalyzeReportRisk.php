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
        $report = $this->report->fresh();
        $analysis = $service->analyze($report);

        // Auto-assign severity from risk level; reviewers can override later.
        $severity = match ($analysis->risk_level) {
            'high', 'critical' => 'high',
            'medium' => 'moderate',
            default => 'low',
        };

        if ($report && $report->severity !== $severity) {
            $report->severity = $severity;
            $report->save();
        }

        $escalations->evaluate($report->fresh(), $analysis);
    }
}
