<x-admin-layout>
    <x-slot name="header">
        {{ __('Contact Messages') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="card card-outline card-primary">
        <div class="card-header">
            <form method="GET" action="{{ route('admin.contact-messages.index') }}" class="w-100">
                <div class="form-row align-items-center">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <label class="sr-only" for="contact-search">{{ __('Search') }}</label>
                        <input type="search" id="contact-search" name="q" value="{{ $search }}"
                            class="form-control" placeholder="{{ __('Search name or message text') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-outline-primary btn-block">
                            <i class="fas fa-filter mr-1"></i> {{ __('Filter') }}
                        </button>
                        @if ($search !== '')
                            <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-link btn-sm">
                                {{ __('Clear') }}
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">{{ __('Name') }}</th>
                            <th scope="col">{{ __('Message') }}</th>
                            <th scope="col">{{ __('Submitted') }}</th>
                            <th scope="col" class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($messages as $message)
                            <tr>
                                <td class="font-weight-bold">{{ $message->first_name }} {{ $message->last_name }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($message->message, 60) }}</td>
                                <td>{{ $message->created_at?->format('M d, Y g:i A') }}</td>
                                <td class="text-right">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.contact-messages.show', $message) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.contact-messages.destroy', $message) }}" method="POST"
                                            data-swal-confirm
                                            data-swal-title="{{ __('Delete message') }}"
                                            data-swal-message="{{ __('Delete this submission?') }}"
                                            data-swal-confirm-button="{{ __('Yes, delete') }}"
                                            data-swal-icon="warning">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    {{ __('No contact messages yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            {{ $messages->links('pagination::bootstrap-4') }}
        </div>
    </div>
</x-admin-layout>
