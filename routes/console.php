<?php

use App\Models\Org;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('orgs:ensure-codes', function (): void {
    $missing = Org::query()
        ->whereNull('org_code')
        ->orWhere('org_code', '')
        ->get();

    if ($missing->isEmpty()) {
        $this->info('All organizations already have public report codes.');
        return;
    }

    $missing->each(function (Org $org): void {
        $org->regenerateReportCode();
        $this->info("Generated code {$org->org_code} for {$org->name}.");
    });
})->purpose('Generate missing organization report codes');
