<x-admin-layout>
    <x-slot name="header">
        {{ __('Report Details') }}
    </x-slot>

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-outline card-primary mb-4">
                <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <span class="badge badge-secondary mr-2 text-uppercase">
                            {{ $report->category }}
                        </span>
                        @if ($report->subcategory)
                            <span class="badge badge-light text-uppercase mr-2 text-dark">
                                {{ $report->subcategory }}
                            </span>
                        @endif
                        @if ($report->urgent)
                            <span class="badge badge-danger mr-2">{{ __('Urgent') }}</span>
                        @endif
                        <span class="badge badge-info text-capitalize">
                            {{ str_replace('_', ' ', $report->status) }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
                        <span class="text-monospace text-muted mr-3">#{{ $report->id }}</span>
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('reports.edit', $report) }}" class="btn btn-outline-primary">
                                <i class="fas fa-edit mr-1"></i> {{ __('Edit') }}
                            </a>
                            <form method="POST" action="{{ route('reports.destroy', $report) }}"
                                data-swal-confirm
                                data-swal-title="{{ __('Delete report') }}"
                                data-swal-message="{{ __('Are you sure you want to delete this report? This action cannot be undone.') }}"
                                data-swal-confirm-button="{{ __('Yes, delete') }}"
                                data-swal-icon="warning">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="fas fa-trash mr-1"></i> {{ __('Delete') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase small mb-1">{{ __('Category') }}</h6>
                            <p class="mb-1">{{ $report->category }}</p>
                            <p class="mb-0 text-muted">
                                <small>{{ $report->subcategory ?? __('Not provided') }}</small>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase small mb-1">{{ __('Type') }}</h6>
                            <p class="mb-0 text-capitalize">{{ $report->type_label }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase small mb-1">{{ __('Severity') }}</h6>
                            <p class="mb-0 text-capitalize">{{ $report->severity_label }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase small mb-1">{{ __('Organization') }}</h6>
                            <p class="mb-0">{{ $report->org?->name ?? __('Unknown') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase small mb-1">{{ __('Submitted') }}</h6>
                            <p class="mb-0">{{ $report->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase small mb-1">{{ __('Violation date') }}</h6>
                            <p class="mb-0">
                                {{ $report->violation_date?->format('M d, Y') ?? __('Not provided') }}
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase small mb-1">{{ __('First response') }}</h6>
                            <p class="mb-0">{{ $report->first_response_at?->format('M d, Y H:i') ?? __('Not yet') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase small mb-1">{{ __('Reporter chat') }}</h6>
                            <a href="{{ route('chat.thread', $report->chat_token) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-comments mr-1"></i> {{ __('Open chat') }}
                            </a>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5 class="mb-3">{{ __('Description') }}</h5>
                        <div class="border rounded p-3 bg-light">
                            <p class="mb-0" style="white-space: pre-line;">{{ $report->description }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5 class="mb-3">{{ __('Reporter Contact') }}</h5>
                        <ul class="list-unstyled mb-0">
                            <li><strong>{{ __('Name') }}:</strong> {{ $report->contact_name ?? __('Not provided') }}</li>
                            <li><strong>{{ __('Email') }}:</strong> {{ $report->contact_email ?? __('Not provided') }}</li>
                            <li><strong>{{ __('Phone') }}:</strong> {{ $report->contact_phone ?? __('Not provided') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-comment-dots mr-2"></i> {{ __('Conversation') }}
                    </h3>
                </div>
                <div class="card-body">
                    @if ($report->messages->isEmpty())
                        <p class="text-muted mb-4">{{ __('No messages yet. Start the conversation below.') }}</p>
                    @else
                        <div class="list-group mb-4">
                            @foreach ($report->messages as $message)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-1 {{ $message->from === 'reviewer' ? 'text-primary' : '' }}">
                                            {{ ucfirst($message->from) }}
                                        </h6>
                                        <small class="text-muted">
                                            <i class="far fa-clock mr-1"></i>{{ $message->created_at->format('M d, Y H:i') }}
                                        </small>
                                    </div>
                                    <p class="mb-0" style="white-space: pre-line;">{{ $message->body }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('reports.message', $report) }}">
                        @csrf
                        <div class="form-group">
                            <label for="body">{{ __('Reply to reporter') }}</label>
                            <textarea id="body" name="body" rows="4" required minlength="2" maxlength="5000"
                                class="form-control @error('body') is-invalid @enderror"
                                placeholder="{{ __('Write a reply to the reporter...') }}">{{ old('body') }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane mr-1"></i> {{ __('Send reply') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-outline card-primary mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0">{{ __('Update Status') }}</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('reports.status', $report) }}">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <label for="status">{{ __('Status') }}</label>
                            <select id="status" name="status" class="form-control">
                                <option value="open" @selected($report->status === 'open')>{{ __('Open') }}</option>
                                <option value="in_review" @selected($report->status === 'in_review')>{{ __('In review') }}</option>
                                <option value="closed" @selected($report->status === 'closed')>{{ __('Closed') }}</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save mr-1"></i> {{ __('Save status') }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="card card-outline card-primary mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-paperclip mr-2"></i> {{ __('Attachments') }}
                    </h3>
                </div>
                <div class="card-body">
                    @if ($report->files->isEmpty())
                        <p class="text-muted mb-0">{{ __('No files uploaded.') }}</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($report->files as $file)
                                @php
                                    $mime = $file->mime ?? '';
                                    $icon = 'fas fa-file';
                                    if (str_starts_with($mime, 'image/')) {
                                        $icon = 'fas fa-file-image text-info';
                                    } elseif (str_starts_with($mime, 'video/')) {
                                        $icon = 'fas fa-file-video text-danger';
                                    } elseif (str_starts_with($mime, 'audio/')) {
                                        $icon = 'fas fa-file-audio text-success';
                                    } elseif (str_contains($mime, 'pdf')) {
                                        $icon = 'fas fa-file-pdf text-danger';
                                    } elseif (str_contains($mime, 'zip')) {
                                        $icon = 'fas fa-file-archive text-warning';
                                    } elseif (str_contains($mime, 'ms-excel') || str_contains($mime, 'spreadsheet')) {
                                        $icon = 'fas fa-file-excel text-success';
                                    } elseif (str_contains($mime, 'msword') || str_contains($mime, 'wordprocessingml')) {
                                        $icon = 'fas fa-file-word text-primary';
                                    } elseif (str_contains($mime, 'presentation')) {
                                        $icon = 'fas fa-file-powerpoint text-warning';
                                    }
                                    $sizeMb = max(0.01, round(($file->size ?? 0) / 1024 / 1024, 2));
                                    $previewUrl = URL::temporarySignedRoute('reports.files.preview', now()->addMinutes(30), [$report, $file]);
                                    $downloadUrl = URL::temporarySignedRoute('reports.files.show', now()->addMinutes(30), [$report, $file]);
                                @endphp
                                <li class="list-group-item">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div class="mr-3">
                                            <i class="{{ $icon }} mr-2"></i>
                                            <span class="font-weight-bold">{{ $file->original_name }}</span>
                                            <small class="text-muted d-block">{{ $mime ?: __('Unknown type') }} • {{ number_format($sizeMb, 2) }} {{ __('MB') }}</small>
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ $downloadUrl }}" class="btn btn-outline-primary" title="{{ __('Download attachment') }}">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="{{ $previewUrl }}" class="btn btn-outline-secondary" target="_blank" rel="noopener" title="{{ __('Open in new tab') }}">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        @if ($file->comment)
                                            <p class="mb-3 text-sm text-muted" style="white-space: pre-line;">
                                                <i class="fas fa-comment-dots mr-1"></i>{{ $file->comment }}
                                            </p>
                                        @endif
                                        @if (str_starts_with($mime, 'audio/'))
                                            <audio controls preload="metadata" class="w-100">
                                                <source src="{{ $previewUrl }}" type="{{ $mime }}">
                                                {{ __('Your browser does not support the audio element.') }}
                                            </audio>
                                        @elseif (str_starts_with($mime, 'video/'))
                                            <video controls preload="metadata" class="w-100 rounded border" style="max-height: 260px;">
                                                <source src="{{ $previewUrl }}" type="{{ $mime }}">
                                                {{ __('Your browser does not support the video element.') }}
                                            </video>
                                        @elseif (str_starts_with($mime, 'image/'))
                                            <img src="{{ $previewUrl }}" alt="{{ $file->original_name }}" class="img-fluid rounded border">
                                        @elseif (str_contains($mime, 'pdf'))
                                            <iframe src="{{ $previewUrl }}" class="w-100 border rounded" style="min-height: 320px;" title="{{ $file->original_name }}"></iframe>
                                        @elseif (str_starts_with($mime, 'text/') || str_contains($mime, 'json') || str_contains($mime, 'xml') || str_contains($mime, 'csv'))
                                            <iframe src="{{ $previewUrl }}" class="w-100 border rounded" style="min-height: 280px;" title="{{ $file->original_name }}"></iframe>
                                        @elseif (str_contains($mime, 'msword') || str_contains($mime, 'wordprocessingml') || str_contains($mime, 'ms-powerpoint') || str_contains($mime, 'presentation') || str_contains($mime, 'ms-excel') || str_contains($mime, 'spreadsheet'))
                                            <iframe src="{{ $previewUrl }}" class="w-100 border rounded" style="min-height: 320px;" title="{{ $file->original_name }}"></iframe>
                                            <p class="mt-2 text-xs text-gray-500">
                                                {{ __('If the preview does not load, use the open button to view this document in a dedicated tab or application.') }}
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-500">
                                                {{ __('Preview not available for this file type. Use one of the buttons above to open or download the file.') }}
                                            </p>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
