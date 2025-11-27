<?php

namespace App\Services;

use App\Models\EscalationEvent;
use App\Models\EscalationRule;
use App\Models\Report;
use App\Models\ReportRiskAnalysis;
use App\Models\User;
use App\Notifications\ReportAlertNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;

class EscalationService
{
    protected array $riskWeights = [
        'low' => 1,
        'medium' => 2,
        'high' => 3,
        'critical' => 4,
    ];

    /**
     * Evaluate escalation rules for a report and apply actions.
     */
    public function evaluate(Report $report, ?ReportRiskAnalysis $analysis = null): void
    {
        $analysis ??= $report->riskAnalysis;

        $rules = EscalationRule::query()
            ->where(function ($query) use ($report): void {
                $query->whereNull('org_id')->orWhere('org_id', $report->org_id);
            })
            ->get();

        if ($rules->isEmpty()) {
            return;
        }

        $riskLevel = $analysis?->risk_level ?? null;
        $riskWeight = $this->riskWeights[$riskLevel] ?? 0;

        foreach ($rules as $rule) {
            if (! $this->matchesRule($rule, $report, $riskLevel, $riskWeight)) {
                continue;
            }

            $actions = $this->applyActions($rule, $report);

            EscalationEvent::create([
                'report_id' => $report->getKey(),
                'escalation_rule_id' => $rule->getKey(),
                'rule_name' => $rule->name,
                'actions' => $actions,
            ]);

            $this->notifyRecipients($rule, $report);
        }
    }

    protected function matchesRule(EscalationRule $rule, Report $report, ?string $riskLevel, int $riskWeight): bool
    {
        $minWeight = $this->riskWeights[$rule->min_risk_level] ?? 0;
        if ($riskWeight < $minWeight) {
            return false;
        }

        if ($rule->match_urgent && ! $report->urgent) {
            return false;
        }

        if ($rule->match_category && strcasecmp($rule->match_category, (string) $report->category) !== 0) {
            return false;
        }

        return true;
    }

    protected function applyActions(EscalationRule $rule, Report $report): array
    {
        $actions = [];

        if ($rule->auto_mark_urgent && ! $report->urgent) {
            $report->urgent = true;
            $report->save();
            $actions[] = 'marked_urgent';
        }

        return $actions;
    }

    protected function notifyRecipients(EscalationRule $rule, Report $report): void
    {
        $roles = Arr::wrap($rule->notify_roles);
        if (empty($roles)) {
            $roles = ['platform_admin', 'executive_admin', 'org_admin'];
        }

        $recipients = User::query()
            ->where('active', true)
            ->whereIn('role', $roles)
            ->where(function ($query) use ($report): void {
                $query->whereNull('org_id')->orWhere('org_id', $report->org_id);
            })
            ->get();

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send(
            $recipients,
            new ReportAlertNotification(
                title: __('Report escalated'),
                message: __('Report #:id has triggered an escalation rule.', ['id' => $report->getKey()]),
                url: route('reports.show', $report),
            )
        );
    }
}
