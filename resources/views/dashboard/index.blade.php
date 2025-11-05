<x-admin-layout>
    <x-slot name="header">
        {{ __('Reviewer Dashboard') }}
    </x-slot>

    <div class="row">
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ number_format($stats['totalReports'] ?? 0) }}</h3>
                    <p>{{ __('Total reports') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <a href="{{ route('reviews.index') }}" class="small-box-footer">
                    {{ __('View reports') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($stats['openReports'] ?? 0) }}</h3>
                    <p>{{ __('Open reports') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <a href="{{ route('reviews.index', ['status' => 'open']) }}" class="small-box-footer">
                    {{ __('Jump to open') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($stats['urgentReports'] ?? 0) }}</h3>
                    <p>{{ __('Urgent reports') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="{{ route('reviews.index', ['urgent' => '1']) }}" class="small-box-footer">
                    {{ __('View urgent queue') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($stats['totalUsers'] ?? 0) }}</h3>
                    <p>{{ __('Users') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                @if (auth()->user()?->hasRole('platform_admin'))
                    <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                        {{ __('Manage users') }} <i class="fas fa-arrow-circle-right"></i>
                    </a>
                @else
                    <span class="small-box-footer text-white-50">
                        {{ __('Active in your org') }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="card card-outline card-light">
        <div class="card-body text-center text-muted">
            {{ __('Use the Reports menu to review and manage submissions in detail.') }}
        </div>
    </div>
</x-admin-layout>
