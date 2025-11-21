<?php

namespace App\Providers;

use App\Console\Commands\TranslationsExportCommand;
use App\Console\Commands\TranslationsImportCommand;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('report-submit', function (Request $request): Limit {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('chat-post', function (Request $request): Limit {
            return Limit::perMinute(10)->by($request->ip());
        });

        $this->commands([
            TranslationsExportCommand::class,
            TranslationsImportCommand::class,
        ]);
    }
}
