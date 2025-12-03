<?php

namespace App\Services;

use App\Models\Report;
use App\Models\ReportFile;
use App\Models\ThreatAssessment;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ThreatAssessmentService
{
    /**
     * Build and persist a threat assessment for a report.
     */
    public function assess(Report $report): ThreatAssessment
    {
        $report->loadMissing(['files', 'riskAnalysis', 'threatAssessment']);

        $text = $this->collectText($report);
        $signals = $this->detectSignals($text, $report);

        $score = $this->calculateScore($signals, $report);
        $level = $this->mapLevel($score);
        $subjectOfConcern = $this->isSubjectOfConcern($signals);
        $recommendation = $this->mapRecommendation($level);

        $summary = $this->buildSummary($signals, $level, $score);

        $assessment = ThreatAssessment::updateOrCreate(
            ['report_id' => $report->getKey()],
            [
                'score' => $score,
                'level' => $level,
                'summary' => $summary,
                'signals' => array_values(array_unique($signals)),
                'recommendation' => $recommendation,
                'subject_of_concern' => $subjectOfConcern,
            ]
        );

        \Log::info('Threat assessment updated.', [
            'report_id' => $report->getKey(),
            'score' => $score,
            'level' => $level,
            'signals' => array_values(array_unique($signals)),
            'recommendation' => $recommendation,
            'subject_of_concern' => $subjectOfConcern,
        ]);

        return $assessment;
    }

    /**
     * Collect concatenated text sources for analysis.
     */
    protected function collectText(Report $report): string
    {
        $parts = [];

        $parts[] = (string) $report->description;
        $parts[] = (string) $report->category;
        $parts[] = (string) $report->subcategory;

        foreach ($report->files ?? [] as $file) {
            if ($file instanceof ReportFile && $file->transcript) {
                $parts[] = $file->transcript;
            }
        }

        if ($report->riskAnalysis) {
            $parts[] = (string) ($report->riskAnalysis->risk_notes ?? '');
        }

        return trim(implode("\n", array_filter($parts)));
    }

    /**
     * Detect signals from text and metadata.
     *
     * @return array<int, string>
     */
    protected function detectSignals(string $text, Report $report): array
    {
        $haystack = Str::lower($text);
        $signals = [];

        $signalKeywords = [
            'weapons' => ['gun', 'knife', 'weapon', 'pistol', 'rifle', 'bomb', 'blade'],
            'violence' => ['threat', 'fight', 'hit', 'attack', 'kill', 'shoot', 'stab', 'assault'],
            'sexual' => ['sexual', 'sex', 'nudity', 'nude', 'harassment', 'assault', 'inappropriate'],
            'self_harm' => ['suicide', 'kill myself', 'self harm', 'cutting', 'overdose'],
            'bullying' => ['bully', 'bullying', 'harass', 'harassment', 'intimidate'],
        ];

        foreach ($signalKeywords as $signal => $keywords) {
            foreach ($keywords as $word) {
                if (Str::contains($haystack, $word)) {
                    $signals[] = $signal;
                    break;
                }
            }
        }

        if ($report->urgent) {
            $signals[] = 'urgent_flag';
        }

        if ($report->riskAnalysis && in_array($report->riskAnalysis->risk_level, ['high', 'critical'], true)) {
            $signals[] = 'high_risk_analysis';
        }

        // Past related reports (same org + contact email/phone)
        $pastCount = $this->pastRelatedReports($report);
        if ($pastCount >= 2) {
            $signals[] = 'repeat_history';
        }

        return $signals;
    }

    protected function pastRelatedReports(Report $report): int
    {
        $contactEmail = $report->contact_email;
        $contactPhone = $report->contact_phone;

        if (! $contactEmail && ! $contactPhone) {
            return 0;
        }

        return (int) DB::table('reports')
            ->where('org_id', $report->org_id)
            ->where('id', '!=', $report->getKey())
            ->where(function ($q) use ($contactEmail, $contactPhone): void {
                if ($contactEmail) {
                    $q->orWhere('contact_email', $contactEmail);
                }
                if ($contactPhone) {
                    $q->orWhere('contact_phone', $contactPhone);
                }
            })
            ->count();
    }

    /**
     * Calculate weighted score.
     */
    protected function calculateScore(array $signals, Report $report): int
    {
        $weights = [
            'weapons' => 40,
            'self_harm' => 40,
            'violence' => 30,
            'sexual' => 20,
            'bullying' => 10,
            'high_risk_analysis' => 15,
            'urgent_flag' => 10,
            'repeat_history' => 10,
        ];

        $score = 0;
        foreach (array_unique($signals) as $signal) {
            $score += $weights[$signal] ?? 0;
        }

        return (int) max(0, min($score, 100));
    }

    protected function mapLevel(int $score): string
    {
        return match (true) {
            $score <= 20 => 'low',
            $score <= 40 => 'moderate',
            $score <= 60 => 'elevated',
            $score <= 80 => 'high',
            default => 'critical',
        };
    }

    protected function mapRecommendation(string $level): string
    {
        return match ($level) {
            'low' => 'Monitor',
            'moderate' => 'Notify counselor',
            'elevated' => 'Notify admin',
            'high' => 'Notify admin immediately',
            'critical' => 'Emergency escalation',
            default => 'Monitor',
        };
    }

    protected function buildSummary(array $signals, string $level, int $score): string
    {
        $labels = array_map(fn ($s) => str_replace('_', ' ', $s), array_unique($signals));
        $signalsText = empty($labels) ? 'no high-risk indicators' : implode(', ', $labels);

        return sprintf(
            'The report contains language related to %s. Based on weighted analysis, the threat level is %s with a score of %d. Reviewer attention recommended.',
            $signalsText,
            ucfirst($level),
            $score
        );
    }

    protected function isSubjectOfConcern(array $signals): bool
    {
        $signals = array_map('strtolower', $signals);
        return ! empty(array_intersect($signals, ['weapons', 'self_harm', 'violence']));
    }
}
