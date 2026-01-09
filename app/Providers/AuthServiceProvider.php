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
        Gate::define('view-all', static fn ($user): bool => $user->isSuperAdmin());

        Gate::define('manage-platform', static fn ($user): bool => $user->isSuperAdmin()
            || $user->isPlatformAdmin());

        Gate::define('manage-org', static fn ($user): bool => $user->isSuperAdmin()
            || $user->isPlatformAdmin()
            || $user->isOrgAdmin());

        Gate::define('review-reports', static fn ($user): bool => $user->isSuperAdmin()
            || $user->isPlatformAdmin()
            || in_array($user->role, [
                'reviewer',
                'security_lead',
                'org_admin',
                'executive_admin',
            ], true));

        Gate::define('manage-categories', static fn ($user): bool => $user->isSuperAdmin()
            || in_array($user->role, ['platform_admin', 'executive_admin'], true));

        Gate::define('manage-data-requests', static fn ($user): bool => $user->isSuperAdmin()
            || in_array($user->role, ['platform_admin', 'executive_admin'], true));
    }
}
