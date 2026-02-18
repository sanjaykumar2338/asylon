<?php

namespace App\Providers;

use App\Console\Commands\TranslationsExportCommand;
use App\Console\Commands\TranslationsImportCommand;
use App\Models\Menu;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
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

        View::composer('layouts.guest', function ($view) {
            if (! Schema::hasTable('menus')) {
                return;
            }
            $headerMenu = Menu::with(['items' => function ($query) {
                $query->orderBy('position')->with('page');
            }])->where('location', 'header')->first();

            $footerMenu = Menu::with(['items' => function ($query) {
                $query->orderBy('position')->with('page');
            }])->where('location', 'footer')->first();

            $view->with([
                'headerMenu' => $headerMenu,
                'footerMenu' => $footerMenu,
            ]);
        });
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
