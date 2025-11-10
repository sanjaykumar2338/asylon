<?php

namespace App\Events;

use App\Models\Report;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportSubmitted
{
    use Dispatchable;
    use SerializesModels;

    public string $baseUrl;

    public function __construct(public Report $report, ?string $baseUrl = null)
    {
        $resolved = trim((string) ($baseUrl ?? ''));

        if ($resolved === '') {
            $resolved = (string) config('app.url', 'http://localhost');
        }

        $this->baseUrl = rtrim($resolved, '/');
    }
}
