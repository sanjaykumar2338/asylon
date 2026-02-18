<x-admin-layout>
    <x-slot name="header">
        {{ __('common.notifications') }}
    </x-slot>

    <div class="container-fluid">
        @include('admin.partials.flash')

        @php $unreadCount = $unreadNotifications->count(); @endphp
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('common.notifications') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Recent activity and alerts across your account.') }}</p>
            </div>
            <span class="badge badge-info badge-pill px-3 py-2">
                {{ __('Unread') }}: {{ $unreadCount }}
            </span>
        </div>

        <div class="card admin-index-card">
            <div class="card-body d-flex flex-wrap align-items-center justify-content-between mb-0 pb-2">
                <h2 class="h6 mb-0 text-uppercase text-muted">{{ __('Recent activity') }}</h2>
                <form method="POST" action="{{ route('notifications.markAllRead') }}" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-check-double mr-2"></i>{{ __('common.mark_all_read') }}
                    </button>
                </form>
            </div>

            <div class="list-group list-group-flush">
                @forelse ($allNotifications as $notification)
                    @php
                        $data = $notification->data ?? [];
                        $title = $data['title'] ?? __('common.notification_single');
                        $message = $data['message'] ?? '';
                        $url = $data['url'] ?? null;
                    @endphp
                    <div class="list-group-item {{ is_null($notification->read_at) ? 'bg-light' : '' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="pr-3">
                                <div class="d-flex align-items-center">
                                    <span class="font-weight-bold">{{ $title }}</span>
                                    @if (is_null($notification->read_at))
                                        <span class="badge badge-primary ml-2">{{ __('New') }}</span>
                                    @endif
                                </div>
                                @if ($message)
                                    <p class="mb-1 text-muted">{{ $message }}</p>
                                @endif
                                <small class="text-muted">{{ $notification->created_at->toDayDateTimeString() }}</small>
                            </div>
                            <div class="text-right">
                                <form method="POST" action="{{ route('notifications.markRead', $notification->getKey()) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-link btn-sm p-0">
                                        {{ $notification->read_at ? __('Viewed') : __('Mark read') }}
                                    </button>
                                </form>
                                @if ($url)
                                    <a href="{{ $url }}" class="btn btn-outline-secondary btn-sm mt-2">
                                        {{ __('Open') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="list-group-item text-center text-muted py-5">
                        {{ __('You have no notifications yet.') }}
                    </div>
                @endforelse
            </div>

            @if ($allNotifications->hasPages())
                <div class="pt-3 px-3">
                    {{ $allNotifications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
