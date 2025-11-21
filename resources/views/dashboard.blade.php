<x-admin-layout>
    <x-slot name="header">
        {{ __('dashboard.title') }}
    </x-slot>

    <div class="row">
        <div class="col-lg-6 col-xl-3 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column">
                    <span class="text-muted text-uppercase small mb-2">{{ __('Welcome') }}</span>
                    <h4 class="font-weight-bold mb-2">{{ Auth::user()->name }}</h4>
                    <p class="text-muted mb-0">{{ __("You're logged in!") }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-3 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column align-items-start">
                    <span class="text-muted text-uppercase small mb-2">{{ __('Quick Links') }}</span>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm mb-2">
                        <i class="fas fa-clipboard-list mr-2"></i> {{ __('View Reports') }}
                    </a>
                    @if (auth()->user()?->hasRole(['org_admin', 'platform_admin']))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-users mr-2"></i> {{ __('Manage Users') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-3 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <span class="text-muted text-uppercase small d-block mb-2">{{ __('Latest Activity') }}</span>
                    <p class="text-muted mb-0">
                        {{ __('Check analytics for organization-wide insights.') }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-3 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <span class="text-muted text-uppercase small d-block mb-2">{{ __('Need help?') }}</span>
                    <p class="text-muted mb-0">
                        {{ __('Reach out to your platform administrator for assistance.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
