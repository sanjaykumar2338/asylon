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
            return Limit::perMinute(5)->by($this->rateLimiterKey($request));
        });

        RateLimiter::for('chat-post', function (Request $request): Limit {
            return Limit::perMinute(10)->by($this->rateLimiterKey($request));
        });

        $this->commands([
            TranslationsExportCommand::class,
            TranslationsImportCommand::class,
        ]);
    }

    /**
     * Build a rate limiter key that respects ultra-private scrubbing.
     */
    protected function rateLimiterKey(Request $request): string
    {
        $privacyHash = (string) $request->attributes->get('privacy.subpoena_hash', '');
        if ($privacyHash !== '') {
            return 'privacy:'.$privacyHash;
        }

        $ip = (string) ($request->ip() ?? '');

        return $ip !== '' ? 'ip:'.$ip : 'anonymous';
    }
}
