<?php

namespace App\Http\Controllers\Admin;

use App\Models\Report;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends AdminController
{
    /**
     * Stream a CSV export of reports that match the current dashboard filters.
     */
    public function reports(Request $request): StreamedResponse
    {
        $fileName = 'reports-export-'.now()->format('Ymd_His').'.csv';

        $query = Report::query()->with('org');
        $this->scopeByRole($query);
        $this->applyFilters($request, $query);

        $query->orderBy('created_at', 'desc');

        $callback = static function () use ($query): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'id',
                'org',
                'type',
                'severity',
                'category',
                'subcategory',
                'status',
                'urgent',
                'submitted_at',
                'violation_date',
                'privacy_status',
            ]);

            foreach ($query->cursor() as $report) {
                $submittedAt = $report->created_at
                    ? $report->created_at->copy()->timezone(config('app.timezone'))->format('Y-m-d H:i:s')
                    : '';

                $violationDate = $report->violation_date
                    ? $report->violation_date->format('Y-m-d')
                    : '';

                fputcsv($handle, [
                    $report->getKey(),
                    $report->org?->name ?? '',
                    $report->type,
                    $report->severity,
                    $report->category,
                    $report->subcategory,
                    $report->status,
                    $report->urgent ? 'yes' : 'no',
                    $submittedAt,
                    $violationDate,
                    $report->privacy_status,
                ]);
            }

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
