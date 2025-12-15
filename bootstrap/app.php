<?php

use App\Http\Middleware\ContentSecurityPolicy;
use App\Http\Middleware\EnsureActiveSubscription;
use App\Http\Middleware\EnsureOrganizationActive;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SetLocaleFromRequest;
use App\Http\Middleware\UltraPrivateMode;
use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\QueueLoggingServiceProvider;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'can' => Authorize::class,
            'setLocale' => SetLocaleFromRequest::class,
            'ultra-private' => UltraPrivateMode::class,
            'active-subscription' => EnsureActiveSubscription::class,
        ]);

        $middleware->append(ContentSecurityPolicy::class);
        $middleware->append(EnsureOrganizationActive::class);
    })
    ->withProviders([
        AppServiceProvider::class,
        AuthServiceProvider::class,
        EventServiceProvider::class,
        QueueLoggingServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
