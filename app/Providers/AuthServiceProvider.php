<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
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
        Gate::define('view-all', static fn ($user): bool => $user->role === 'platform_admin');

        Gate::define('manage-platform', static fn ($user): bool => $user->role === 'platform_admin');

        Gate::define('manage-org', static fn ($user): bool => in_array($user->role, [
            'org_admin',
            'platform_admin',
            'executive_admin',
        ], true));

        Gate::define('review-reports', static fn ($user): bool => in_array($user->role, [
            'reviewer',
            'security_lead',
            'org_admin',
            'platform_admin',
        ], true));

        Gate::define('manage-categories', static fn ($user): bool => in_array($user->role, [
            'platform_admin',
            'executive_admin',
        ], true));

        Gate::define('manage-data-requests', static fn ($user): bool => in_array($user->role, [
            'platform_admin',
            'executive_admin',
        ], true));
    }
}
