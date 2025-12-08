<?php

namespace App\Http\Controllers\Admin;


use App\Models\AuditLog;
use App\Models\Report;
use App\Services\Audit;
use App\Support\AuditLogger;
use App\Support\ReportExporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends AdminController
{
    public function __construct(protected readonly ReportExporter $exporter)
    {
    }

    /**
     * Stream a CSV export of reports that match the current dashboard filters.
     */
    public function reports(Request $request): StreamedResponse
    {
        $fileName = 'reports-export-'.now()->format('Ymd_His').'.csv';

        $query = Report::query()->with(['org', 'riskAnalysis']);
        $this->scopeByRole($query);
        $this->applyFilters($request, $query);

        $query->orderBy('created_at', 'desc');

        $exporter = $this->exporter;

        $callback = static function () use ($query, $exporter): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, $exporter->listHeaders());

            foreach ($query->cursor() as $report) {
                fputcsv($handle, $exporter->listRow($report));
            }

            fclose($handle);
        };

        Audit::log('reviewer', 'export_report_list', 'report_export', 'list', [
            'filters' => $request->query(),
        ]);

        AuditLogger::log([
            'org_id' => $request->user()?->org_id,
            'user_id' => $request->user()?->id,
            'action' => 'case_list_export_csv',
            'meta' => [
                'filters' => $request->query(),
            ],
        ]);

        return response()->streamDownload(
            $callback,
            $fileName,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]
        );
    }

    /**
     * Stream a PDF export of reports matching current filters.
     */
    public function reportsPdf(Request $request): StreamedResponse
    {
        $fileName = 'reports-export-'.now()->format('Ymd_His').'.pdf';

        $query = Report::query()->with(['org', 'riskAnalysis']);
        $this->scopeByRole($query);
        $this->applyFilters($request, $query);

        $query->orderBy('created_at', 'desc');

        $exporter = $this->exporter;

        Audit::log('reviewer', 'export_report_list_pdf', 'report_export', 'list', [
            'filters' => $request->query(),
        ]);

        AuditLogger::log([
            'org_id' => $request->user()?->org_id,
            'user_id' => $request->user()?->id,
            'action' => 'case_list_export_pdf',
            'meta' => [
                'filters' => $request->query(),
            ],
        ]);

        return response()->streamDownload(
            static function () use ($query, $exporter): void {
                echo $exporter->listPdf($query->cursor());
            },
            $fileName,
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }

    /**
     * Export a single report as CSV.
     */
    public function reportCsv(Request $request, Report $report): StreamedResponse
    {
        $this->authorizeReport($request, $report);

        $report->loadMissing([
            'org',
            'riskAnalysis',
            'files',
            'notes.user',
            'messages',
            'resolver',
            'escalationEvents',
        ]);

        $fileName = 'report-'.$report->getKey().'.csv';
        $headers = $this->exporter->singleHeaders();
        $row = $this->exporter->singleRow($report);

        Audit::log('reviewer', 'export_report_csv', 'report', $report->getKey());
        AuditLogger::caseAction($request->user(), $report, 'case_export_csv', [
            'export_type' => 'single_csv',
        ]);

        $callback = static function () use ($headers, $row): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            fputcsv($handle, $row);
            fclose($handle);
        };

        return response()->streamDownload(
            $callback,
            $fileName,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]
        );
    }

    /**
     * Export a single report as PDF.
     */
    public function reportPdf(Request $request, Report $report): StreamedResponse
    {
        $this->authorizeReport($request, $report);

        $report->loadMissing([
            'org',
            'riskAnalysis',
            'files',
            'notes.user',
            'messages',
            'resolver',
            'escalationEvents',
        ]);

        $fileName = 'report-'.$report->getKey().'.pdf';
        $exporter = $this->exporter;

        Audit::log('reviewer', 'export_report_pdf', 'report', $report->getKey());
        AuditLogger::caseAction($request->user(), $report, 'case_export_pdf', [
            'export_type' => 'single_pdf',
        ]);

        return response()->streamDownload(
            static function () use ($exporter, $report): void {
                echo $exporter->casePdf($report);
            },
            $fileName,
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }

    /**
     * Export a full audit packet for a report as PDF.
     */
    public function auditPacket(Request $request, Report $report): StreamedResponse
    {
        $this->authorizeReport($request, $report);

        $report->loadMissing([
            'org',
            'riskAnalysis',
            'files',
            'notes.user',
            'messages',
            'resolver',
            'escalationEvents',
        ]);

        $auditLogs = AuditLog::query()
            ->with('user')
            ->where(function ($query) use ($report): void {
                $query->where('case_id', $report->getKey())
                    ->orWhere(function ($sub) use ($report): void {
                        $sub->where('target_type', 'report')
                            ->where('target_id', $report->getKey());
                    });
            })
            ->orderBy('created_at')
            ->get();

        $fileName = 'report-'.$report->getKey().'-audit.pdf';
        $exporter = $this->exporter;

        Audit::log('reviewer', 'export_report_audit_pdf', 'report', $report->getKey());
        AuditLogger::caseAction($request->user(), $report, 'case_export_audit_packet', [
            'export_type' => 'audit_packet',
        ]);

        return response()->streamDownload(
            static function () use ($exporter, $report, $auditLogs): void {
                echo $exporter->auditPacketPdf($report, $auditLogs);
            },
            $fileName,
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }

    /**
     * Authorize access to a report for export based on role and org.
     */
    protected function authorizeReport(Request $request, Report $report): void
    {
        $user = $request->user();

        if (! $user || (! $user->hasRole('platform_admin') && $user->org_id !== $report->org_id)) {
            abort(403);
        }
    }

    /**
     * Apply dashboard filters to the export query.
     */
    protected function applyFilters(Request $request, Builder $query): void
    {
        $status = (string) $request->query('status', '');
        $urgent = (string) $request->query('urgent', '');
        $category = (string) $request->query('category', '');
        $subcategory = (string) $request->query('subcategory', '');
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');
        $violationFrom = (string) $request->query('violation_from', '');
        $violationTo = (string) $request->query('violation_to', '');
        $type = (string) $request->query('type', '');
        $severity = (string) $request->query('severity', '');
        $riskLevel = (string) $request->query('risk_level', '');

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($urgent !== '') {
            $query->where('urgent', $urgent === '1');
        }

        if ($category !== '') {
            $query->where('category', $category);
        }

        if ($subcategory !== '') {
            $query->where('subcategory', $subcategory);
        }

        if ($type !== '') {
            $query->where('type', $type);
        }

        if ($severity !== '') {
            $query->where('severity', $severity);
        }

        if ($riskLevel !== '') {
            if ($riskLevel === 'unscored') {
                $query->whereDoesntHave('riskAnalysis');
            } else {
                $query->whereHas('riskAnalysis', fn ($q) => $q->where('risk_level', $riskLevel));
            }
        }

        if ($from !== '' && Carbon::hasFormat($from, 'Y-m-d')) {
            $query->whereDate('created_at', '>=', Carbon::createFromFormat('Y-m-d', $from));
        }

        if ($to !== '' && Carbon::hasFormat($to, 'Y-m-d')) {
            $query->whereDate('created_at', '<=', Carbon::createFromFormat('Y-m-d', $to));
        }

        if ($violationFrom !== '' && Carbon::hasFormat($violationFrom, 'Y-m-d')) {
            $query->whereDate('violation_date', '>=', Carbon::createFromFormat('Y-m-d', $violationFrom));
        }

        if ($violationTo !== '' && Carbon::hasFormat($violationTo, 'Y-m-d')) {
            $query->whereDate('violation_date', '<=', Carbon::createFromFormat('Y-m-d', $violationTo));
        }
    }
}
