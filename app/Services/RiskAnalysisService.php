<?php

namespace App\Services;

use App\Models\Report;
use App\Models\ReportRiskAnalysis;

class RiskAnalysisService
{
    /**
     * Analyze a report using simple keyword rules.
     */
    public function analyze(Report $report): ReportRiskAnalysis
    {
        $text = strtolower(trim(implode(' ', array_filter([
            $report->description ?? '',
            $report->category ?? '',
            $report->subcategory ?? '',
        ]))));

        $keywordSets = [
            'self_harm' => ['suicide', 'kill myself', 'self harm', 'cutting', 'end my life'],
            'weapon' => ['gun', 'knife', 'weapon', 'shooting', 'bomb'],
            'threat' => ['threat', 'attack', 'violence', 'hurt them', 'hurt you'],
            'bullying' => ['bully', 'bullying', 'harass', 'harassment', 'teasing'],
        ];

        $matchedKeywords = [];
        $signals = [
            'self_harm' => false,
            'weapon' => false,
            'threat' => false,
            'bullying' => false,
        ];

        $score = 0;

        foreach ($keywordSets as $signalKey => $words) {
            foreach ($words as $word) {
                if ($word !== '' && str_contains($text, $word)) {
                    $matchedKeywords[] = $word;
                    $signals[$signalKey] = true;

                    // Weights by category of signal.
                    if (in_array($signalKey, ['self_harm', 'weapon', 'threat'], true)) {
                        $score += 40;
                    } else {
                        $score += 15;
                    }
                }
            }
        }

        $score = max(0, min(100, $score));

        if ($score >= 70) {
            $level = 'high';
        } elseif ($score >= 40) {
            $level = 'medium';
        } else {
            $level = 'low';
        }

        return ReportRiskAnalysis::updateOrCreate(
            ['report_id' => $report->getKey()],
            [
                'risk_score' => $score,
                'risk_level' => $level,
                'matched_keywords' => array_values(array_unique($matchedKeywords)),
                'signals' => $signals,
            ]
        );
    }
}
