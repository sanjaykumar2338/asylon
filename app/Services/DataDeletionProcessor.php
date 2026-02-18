<?php

namespace App\Services;

use App\Models\DataDeletionRequest;
use App\Models\Report;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Log;

class DataDeletionProcessor
{
    public function process(DataDeletionRequest $request, User $actor): void
    {
        switch ($request->scope) {
            case 'reporter_pii':
                $this->anonymizeReporterPii($request);
                break;
            case 'cases':
                $this->anonymizeCase($request);
                break;
            case 'account':
                // Placeholder: no account deletion yet.
                break;
        }

        $request->status = 'completed';
        $request->processed_at = now();
        $request->processed_by = $actor->getKey();
        $request->save();

        AuditLogger::log([
            'org_id' => $request->org_id,
            'user_id' => $actor->getKey(),
            'action' => 'data_request_completed',
            'meta' => [
                'scope' => $request->scope,
                'request_id' => $request->getKey(),
            ],
        ]);
    }

    protected function anonymizeReporterPii(DataDeletionRequest $request): void
    {
        $email = trim((string) $request->requester_email);
        $phone = trim((string) $request->requester_phone);

        if ($email === '' && $phone === '') {
            Log::warning('Data deletion request missing identifiers for reporter PII', [
                'request_id' => $request->getKey(),
            ]);

            return;
        }

        $query = Report::query()
            ->where(function ($q) use ($email, $phone): void {
                if ($email !== '') {
                    $q->orWhere('contact_email', $email);
                }

                if ($phone !== '') {
                    $q->orWhere('contact_phone', $phone);
                }
            });

        if ($request->org_id) {
            $query->where('org_id', $request->org_id);
        }

        $query->chunkById(200, function ($reports): void {
            foreach ($reports as $report) {
                $report->contact_name = null;
                $report->contact_email = null;
                $report->contact_phone = null;
                $report->save();
            }
        });
    }

    protected function anonymizeCase(DataDeletionRequest $request): void
    {
        if ($request->reference_type !== 'case' || ! $request->reference_value) {
            return;
        }

        $report = Report::find($request->reference_value);

        if (! $report) {
            return;
        }

        $report->contact_name = null;
        $report->contact_email = null;
        $report->contact_phone = null;
        $report->save();
    }
}
