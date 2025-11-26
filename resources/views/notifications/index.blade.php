<x-admin-layout>
    <x-slot name="header">
        {{ __('common.notifications') }}
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-0">{{ __('Recent activity') }}</h5>
                        @php $unreadCount = $unreadNotifications->count(); @endphp
                        <small class="text-muted d-block">
                            {{ $unreadCount }}
                            {{ \Illuminate\Support\Str::plural(__('unread notification'), $unreadCount) }}
                        </small>
                    </div>
                    <form method="POST" action="{{ route('notifications.markAllRead') }}">
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
                    <div class="card-footer">
                        {{ $allNotifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
