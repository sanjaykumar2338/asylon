<?php

namespace App\Support;

use App\Models\AuditLog;
use App\Models\Report;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ReportExporter
{
    /**
     * Column headers for list exports.
     *
     * @return array<int, string>
     */
    public function listHeaders(): array
    {
        return [
            'id',
            'org',
            'type',
            'severity',
            'category',
            'subcategory',
            'status',
            'urgent',
            'risk_level',
            'risk_score',
            'submitted_at',
            'violation_date',
            'privacy_status',
        ];
    }

    /**
     * Build a single CSV row for a report list export.
     *
     * @return array<int, string|int|null>
     */
    public function listRow(Report $report): array
    {
        $analysis = $report->riskAnalysis;

        return [
            $report->getKey(),
            $report->org?->name ?? '',
            $report->type ?? '',
            $report->severity ?? '',
            $report->category ?? '',
            $report->subcategory ?? '',
            $report->status ?? '',
            $report->urgent ? 'yes' : 'no',
            $analysis?->risk_level ?? '',
            $analysis?->risk_score ?? '',
            $this->formatDateTime($report->created_at),
            $this->formatDate($report->violation_date),
            $report->privacy_status,
        ];
    }

    /**
     * Column headers for a detailed, single-report CSV export.
     *
     * @return array<int, string>
     */
    public function singleHeaders(): array
    {
        return [
            'id',
            'org',
            'type',
            'severity',
            'category',
            'subcategory',
            'status',
            'urgent',
            'risk_level',
            'risk_score',
            'risk_keywords',
            'submitted_at',
            'violation_date',
            'privacy_status',
            'portal_source',
            'contact_name',
            'contact_email',
            'contact_phone',
            'description',
            'status_note',
            'resolved_by',
            'files',
            'notes',
            'messages',
            'escalations',
        ];
    }

    /**
     * Build a detailed CSV row for a single report.
     *
     * @return array<int, string|null>
     */
    public function singleRow(Report $report): array
    {
        $analysis = $report->riskAnalysis;

        return [
            $report->getKey(),
            $report->org?->name ?? '',
            $report->type ?? '',
            $report->severity ?? '',
            $report->category ?? '',
            $report->subcategory ?? '',
            $report->status ?? '',
            $report->urgent ? 'yes' : 'no',
            $analysis?->risk_level ?? '',
            $analysis?->risk_score ?? '',
            $this->joinList($analysis?->matched_keywords ?? []),
            $this->formatDateTime($report->created_at),
            $this->formatDate($report->violation_date),
            $report->privacy_status,
            $report->portal_source ?? '',
            $report->contact_name ?? '',
            $report->contact_email ?? '',
            $report->contact_phone ?? '',
            trim((string) $report->description),
            $report->status_note ?? '',
            $report->resolver?->name ?? '',
            $this->formatFilesForCsv($report),
            $this->formatNotesForCsv($report),
            $this->formatMessagesForCsv($report),
            $this->formatEscalationsForCsv($report),
        ];
    }

    /**
     * Render a simple, text-based PDF for a single case.
     */
    public function casePdf(Report $report): string
    {
        $pdf = new SimplePdf();

        $this->addHeader($pdf, 'Case Report', $report);
        $this->addCaseSections($pdf, $report);

        return $pdf->output();
    }

    /**
     * Render a PDF list export respecting applied filters.
     *
     * @param  iterable<Report>  $reports
     */
    public function listPdf(iterable $reports): string
    {
        $pdf = new SimplePdf();

        $pdf->addHeading('Report Export');
        $pdf->addLine('Generated: '.$this->formatDateTime(Carbon::now()), 10);
        $pdf->addSpacer();

        foreach ($reports as $report) {
            $analysis = $report->riskAnalysis;

            $pdf->addSubheading('Case #'.$report->getKey());
            $this->addKeyValueBlock($pdf, [
                'Organization' => $report->org?->name ?? 'Unassigned',
                'Type' => $this->label($report->type),
                'Category' => $this->label($report->category),
                'Subcategory' => $this->label($report->subcategory),
                'Status' => $this->label($report->status),
                'Severity' => $this->label($report->severity),
                'Risk' => $analysis ? strtoupper((string) $analysis->risk_level).' ('.$analysis->risk_score.')' : 'Unscored',
                'Urgent' => $report->urgent ? 'Yes' : 'No',
                'Submitted' => $this->formatDateTime($report->created_at),
                'Violation Date' => $this->formatDate($report->violation_date),
            ]);

            $pdf->addSpacer();
        }

        return $pdf->output();
    }

    /**
     * Render an audit-ready PDF packet containing case details and activity trail.
     *
     * @param  Collection<int, AuditLog>  $auditLogs
     */
    public function auditPacketPdf(Report $report, Collection $auditLogs): string
    {
        $pdf = new SimplePdf();

        $this->addHeader($pdf, 'Audit Packet', $report);
        $this->addCaseSections($pdf, $report);
        $this->addAuditTrail($pdf, $auditLogs);

        return $pdf->output();
    }

    protected function addHeader(SimplePdf $pdf, string $title, Report $report): void
    {
        $pdf->addHeading($title);
        $pdf->addLine('Case ID: '.$report->getKey(), 12);
        $pdf->addLine('Generated: '.$this->formatDateTime(Carbon::now()), 10);
        $pdf->addSpacer();
    }

    protected function addCaseSections(SimplePdf $pdf, Report $report): void
    {
        $analysis = $report->riskAnalysis;

        $pdf->addSubheading('Case Overview');
        $this->addKeyValueBlock($pdf, [
            'Organization' => $report->org?->name ?? 'Unassigned',
            'Submitted' => $this->formatDateTime($report->created_at),
            'Violation Date' => $this->formatDate($report->violation_date),
            'Type' => $this->label($report->type),
            'Category' => $this->label($report->category),
            'Subcategory' => $this->label($report->subcategory),
            'Status' => $this->label($report->status),
            'Severity' => $this->label($report->severity),
            'Urgent' => $report->urgent ? 'Yes' : 'No',
            'Risk Level' => $analysis ? strtoupper((string) $analysis->risk_level).' ('.$analysis->risk_score.')' : 'Unscored',
            'Portal Source' => $this->label($report->portal_source),
            'Privacy' => $report->privacy_status,
            'Resolved By' => $report->resolver?->name ?? 'Unassigned',
        ]);

        $pdf->addSubheading('Reporter Details');
        $this->addKeyValueBlock($pdf, [
            'Name' => $report->contact_name ?: 'Anonymous',
            'Email' => $report->contact_email ?: 'Not provided',
            'Phone' => $report->contact_phone ?: 'Not provided',
        ]);

        $pdf->addSubheading('Description');
        $pdf->addLine($report->description ?: 'No description provided.', 11, 1);

        $pdf->addSubheading('Risk Analysis');
        if ($analysis) {
            $this->addKeyValueBlock($pdf, [
                'Risk Level' => strtoupper((string) $analysis->risk_level),
                'Risk Score' => (string) $analysis->risk_score,
                'Matched Keywords' => $this->joinList($analysis->matched_keywords ?? []),
            ]);
        } else {
            $pdf->addLine('Risk scoring not available for this report.', 11, 1);
        }

        $pdf->addSubheading('Attachments');
        $files = $report->files ?? collect();
        if ($files->isEmpty()) {
            $pdf->addLine('None attached.', 11, 1);
        } else {
            foreach ($files as $file) {
                $details = trim($file->original_name.' ('.$file->mime.')');
                $size = $this->formatFileSize($file->size ?? 0);
                $comment = $file->comment ? ' - '.$file->comment : '';
                $pdf->addLine($details.' '.$size.$comment, 11, 1);
            }
        }

        $pdf->addSubheading('Internal Notes');
        $notes = $report->notes ?? collect();
        if ($notes->isEmpty()) {
            $pdf->addLine('No notes recorded.', 11, 1);
        } else {
            foreach ($notes->sortBy('created_at') as $note) {
                $author = $note->user?->name ? ' by '.$note->user->name : '';
                $pdf->addLine(
                    '['.$this->formatDateTime($note->created_at).']'.$author.': '.trim((string) $note->body),
                    11,
                    1
                );
            }
        }

        $pdf->addSubheading('Conversation');
        $messages = $report->messages ?? collect();
        if ($messages->isEmpty()) {
            $pdf->addLine('No secure messages exchanged.', 11, 1);
        } else {
            foreach ($messages->sortBy('sent_at') as $message) {
                $side = $this->label($message->side ?? 'message');
                $pdf->addLine(
                    '['.$this->formatDateTime($message->sent_at).'] '.$side.': '.trim((string) $message->message),
                    11,
                    1
                );
            }
        }

        $this->addEscalations($pdf, $report);
    }

    protected function addEscalations(SimplePdf $pdf, Report $report): void
    {
        $pdf->addSubheading('Escalation Events');
        $escalations = $report->escalationEvents ?? collect();

        if ($escalations->isEmpty()) {
            $pdf->addLine('No automatic escalations recorded.', 11, 1);
            return;
        }

        foreach ($escalations as $event) {
            $actions = $this->joinList((array) ($event->actions ?? []));
            $label = $event->rule_name ?: 'Escalation';
            $pdf->addLine($label.($actions ? ' - '.$actions : ''), 11, 1);
        }
    }

    /**
     * @param  Collection<int, AuditLog>  $auditLogs
     */
    protected function addAuditTrail(SimplePdf $pdf, Collection $auditLogs): void
    {
        $pdf->addSubheading('Audit Trail');

        if ($auditLogs->isEmpty()) {
            $pdf->addLine('No audit activity recorded for this case.', 11, 1);
            return;
        }

        foreach ($auditLogs as $log) {
            $pdf->addLine($this->formatAuditLine($log), 11, 1);
        }
    }

    protected function formatAuditLine(AuditLog $log): string
    {
        $timestamp = $this->formatDateTime($log->created_at);
        $title = $this->auditTitle($log);
        $actor = $this->auditActor($log);
        $description = $this->auditDescription($log);

        $parts = array_filter([
            '['.$timestamp.']',
            $title,
            $actor ? 'Actor: '.$actor : null,
            $description !== '' ? $description : null,
        ]);

        return implode(' - ', $parts);
    }

    protected function auditTitle(AuditLog $log): string
    {
        return match ($log->action) {
            'case_view' => 'Case viewed',
            'case_update' => 'Case updated',
            'case_export_csv' => 'Case exported (CSV)',
            'case_export_pdf' => 'Case exported (PDF)',
            'case_export_audit_packet' => 'Audit packet exported',
            'case_list_export_csv' => 'List exported (CSV)',
            'case_list_export_pdf' => 'List exported (PDF)',
            'portal_submission' => 'Report submitted',
            'alert_dispatched' => 'Urgent alert sent',
            'view_report' => 'Report viewed',
            'update_report' => 'Report updated',
            'change_status' => 'Status changed',
            'post_message' => 'Reviewer replied',
            'post_followup_message' => 'Reporter follow-up',
            'first_response' => 'First response sent',
            'reporter_followup_alert' => 'Reporter follow-up alerts sent',
            'download_attachment' => 'Attachment downloaded',
            'preview_attachment' => 'Attachment previewed',
            'download_followup_attachment' => 'Reporter downloaded attachment',
            'preview_followup_attachment' => 'Reporter previewed attachment',
            'trash_report' => 'Report moved to trash',
            'restore_report' => 'Report restored',
            default => Str::headline(str_replace('_', ' ', $log->action ?? 'Activity')),
        };
    }

    protected function auditDescription(AuditLog $log): string
    {
        $meta = $log->meta ?? [];
        $actor = $this->auditActor($log) ?? 'System';

        return match ($log->action) {
            'case_view' => 'Viewed by '.$actor,
            'case_update' => $this->buildStatusChangeDescription($meta, $actor),
            'case_export_csv' => 'Case CSV exported by '.$actor,
            'case_export_pdf' => 'Case PDF exported by '.$actor,
            'case_export_audit_packet' => 'Audit packet exported by '.$actor,
            'case_list_export_csv' => 'Filtered list (CSV) exported by '.$actor,
            'case_list_export_pdf' => 'Filtered list (PDF) exported by '.$actor,
            'portal_submission' => 'Submitted via '.($meta['portal_source'] ?? 'portal').' ('.$meta['type'] ?? 'unspecified'.')',
            'alert_dispatched' => 'Urgent alert dispatched (channel: '.($meta['channel'] ?? 'unknown').')',
            'update_report' => 'Updated by '.$actor,
            'change_status' => $this->buildStatusChangeDescription($meta, $actor),
            'post_message' => 'Reply sent by '.$actor,
            'post_followup_message' => 'Reporter follow-up message',
            'first_response' => 'First response sent by '.$actor,
            'reporter_followup_alert' => 'Alerts sent (emails: '.($meta['emails_sent'] ?? 0).', sms: '.($meta['sms_sent'] ?? 0).')',
            'download_attachment', 'preview_attachment' => 'Attachment accessed by '.$actor,
            'download_followup_attachment', 'preview_followup_attachment' => 'Reporter accessed attachment',
            'trash_report' => 'Moved to trash by '.$actor,
            'restore_report' => 'Restored by '.$actor,
            default => '',
        };
    }

    protected function auditActor(AuditLog $log): ?string
    {
        if ($log->user?->name) {
            $role = $log->user->role ? ' ('.$log->user->role.')' : '';

            return $log->user->name.$role;
        }

        return match ($log->actor_type) {
            'reviewer' => 'Reviewer',
            'reporter' => 'Reporter',
            'system' => 'System',
            default => null,
        };
    }

    protected function buildStatusChangeDescription(array $meta, string $actor): string
    {
        $from = $meta['from_status'] ?? $meta['from'] ?? 'unknown';
        $to = $meta['to_status'] ?? $meta['to'] ?? 'unknown';
        $note = $meta['note'] ?? null;
        $resolvedBy = $meta['resolved_by_name'] ?? null;

        $parts = [
            'Status changed from '.$from.' to '.$to.' by '.$actor,
        ];

        if ($resolvedBy) {
            $parts[] = 'Resolved by '.$resolvedBy;
        }

        if ($note) {
            $parts[] = 'Note: '.$note;
        }

        return implode(' | ', $parts);
    }

    protected function addKeyValueBlock(SimplePdf $pdf, array $pairs, int $indent = 1): void
    {
        foreach ($pairs as $label => $value) {
            $pdf->addLine($label.': '.$this->valueOrDefault($value), 11, $indent);
        }
    }

    protected function valueOrDefault(mixed $value, string $default = 'N/A'): string
    {
        if ($value === null) {
            return $default;
        }

        $string = trim((string) $value);

        return $string === '' ? $default : $string;
    }

    protected function label(?string $value): string
    {
        if ($value === null || trim($value) === '') {
            return 'Unspecified';
        }

        return ucfirst(trim($value));
    }

    protected function formatDateTime(?Carbon $value): string
    {
        if (! $value) {
            return '';
        }

        return $value->copy()->timezone(config('app.timezone'))->format('Y-m-d H:i:s');
    }

    protected function formatDate(?Carbon $value): string
    {
        if (! $value) {
            return '';
        }

        return $value->format('Y-m-d');
    }

    protected function formatFileSize(int $bytes): string
    {
        if ($bytes <= 0) {
            return '';
        }

        $kilobytes = $bytes / 1024;

        if ($kilobytes < 1024) {
            return number_format($kilobytes, 1).' KB';
        }

        return number_format($kilobytes / 1024, 2).' MB';
    }

    protected function joinList(array $items): string
    {
        $values = array_filter(array_map('trim', $items), static fn ($value) => $value !== '');

        return implode(', ', $values);
    }

    protected function formatFilesForCsv(Report $report): string
    {
        $files = $report->files ?? collect();

        if ($files->isEmpty()) {
            return '';
        }

        return $files
            ->map(function ($file): string {
                $parts = [
                    $file->original_name ?? 'file',
                    $file->mime ? '('.$file->mime.')' : null,
                    $this->formatFileSize((int) ($file->size ?? 0)),
                    $file->comment ? '- '.$file->comment : null,
                ];

                return implode(' ', array_filter($parts, static fn ($part) => $part !== null && $part !== ''));
            })
            ->implode(' | ');
    }

    protected function formatNotesForCsv(Report $report): string
    {
        $notes = $report->notes ?? collect();

        if ($notes->isEmpty()) {
            return '';
        }

        return $notes
            ->sortBy('created_at')
            ->map(function ($note): string {
                $author = $note->user?->name ? ' by '.$note->user->name : '';

                return '['.$this->formatDateTime($note->created_at).']'.$author.': '.trim((string) $note->body);
            })
            ->implode(' | ');
    }

    protected function formatMessagesForCsv(Report $report): string
    {
        $messages = $report->messages ?? collect();

        if ($messages->isEmpty()) {
            return '';
        }

        return $messages
            ->sortBy('sent_at')
            ->map(function ($message): string {
                $side = $this->label($message->side ?? 'message');
                $timestamp = $this->formatDateTime($message->sent_at);

                return '['.$timestamp.'] '.$side.': '.trim((string) $message->message);
            })
            ->implode(' | ');
    }

    protected function formatEscalationsForCsv(Report $report): string
    {
        $escalations = $report->escalationEvents ?? collect();

        if ($escalations->isEmpty()) {
            return '';
        }

        return $escalations
            ->map(function ($event): string {
                $actions = $this->joinList((array) ($event->actions ?? []));
                $label = $event->rule_name ?: 'Escalation';

                return $label.($actions ? ' - '.$actions : '');
            })
            ->implode(' | ');
    }
}
