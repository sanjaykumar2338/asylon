<x-admin-layout>
    <x-slot name="header">
        {{ __('Review Queue') }}
    </x-slot>

    @if (session('ok'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('ok') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card card-outline card-primary mb-4">
        <div class="card-header">
            <h3 class="card-title mb-0">
                <i class="fas fa-filter mr-2"></i> {{ __('Filter Reports') }}
            </h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reviews.index') }}">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="status">{{ __('Status') }}</label>
                        <select id="status" name="status" class="form-control">
                            <option value="">{{ __('All') }}</option>
                            <option value="open" @selected($status === 'open')>{{ __('Open') }}</option>
                            <option value="in_review" @selected($status === 'in_review')>{{ __('In review') }}</option>
                            <option value="closed" @selected($status === 'closed')>{{ __('Closed') }}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="urgent">{{ __('Urgent') }}</label>
                        <select id="urgent" name="urgent" class="form-control">
                            <option value="">{{ __('All') }}</option>
                            <option value="1" @selected($urgent === '1')>{{ __('Yes') }}</option>
                            <option value="0" @selected($urgent === '0')>{{ __('No') }}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="category">{{ __('Category') }}</label>
                        <input list="categories" id="category" name="category" value="{{ $category }}" class="form-control"
                            placeholder="{{ __('Any category') }}">
                        <datalist id="categories">
                            @foreach ($categories as $item)
                                <option value="{{ $item }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="from">{{ __('Submitted From') }}</label>
                        <input type="date" id="from" name="from" value="{{ $from }}" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="to">{{ __('Submitted To') }}</label>
                        <input type="date" id="to" name="to" value="{{ $to }}" class="form-control">
                    </div>
                </div>
                <div class="d-flex flex-column flex-sm-row justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary mr-sm-2 mb-2 mb-sm-0">
                        <i class="fas fa-search mr-1"></i> {{ __('Apply') }}
                    </button>
                    <a href="{{ route('reviews.index') }}" class="btn btn-outline-secondary">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
            <h3 class="card-title mb-0">
                <i class="fas fa-clipboard-list mr-2"></i> {{ __('Reports') }}
            </h3>
            <span class="badge badge-info badge-pill px-3 py-2 mt-3 mt-lg-0">
                {{ __('Results') }}: {{ number_format($reports->total()) }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">#</th>
                        @can('view-all')
                            <th scope="col">{{ __('Organization') }}</th>
                        @endcan
                        <th scope="col">{{ __('Category') }}</th>
                        <th scope="col">{{ __('Urgent') }}</th>
                        <th scope="col">{{ __('Status') }}</th>
                        <th scope="col">{{ __('Attachments') }}</th>
                        <th scope="col">{{ __('Submitted') }}</th>
                        <th scope="col" class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $report)
                        @php
                            $modalId = 'attachmentsModal-' . \Illuminate\Support\Str::slug($report->getKey(), '-');
                        @endphp
                        <tr>
                            <td class="text-monospace text-muted">#{{ $report->id }}</td>
                            @can('view-all')
                                <td>{{ $report->org?->name ?? __('Unknown') }}</td>
                            @endcan
                            <td class="font-weight-bold">{{ $report->category }}</td>
                            <td>
                                @if ($report->urgent)
                                    <span class="badge badge-danger">{{ __('Yes') }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ __('No') }}</span>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('reports.status', $report) }}" class="form-inline">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="custom-select custom-select-sm" onchange="this.form.submit()">
                                        <option value="open" @selected($report->status === 'open')>{{ __('Open') }}</option>
                                        <option value="in_review" @selected($report->status === 'in_review')>{{ __('In review') }}</option>
                                        <option value="closed" @selected($report->status === 'closed')>{{ __('Closed') }}</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <span class="badge badge-light">
                                    <i class="fas fa-paperclip mr-1"></i> {{ $report->files_count }}
                                </span>
                            </td>
                            <td>{{ $report->created_at->format('M d, Y H:i') }}</td>
                            <td class="text-right">
                                <div class="d-flex flex-wrap justify-content-end">
                                    <div class="btn-group btn-group-sm mb-1" role="group">
                                        <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#{{ $modalId }}">
                                            <i class="fas fa-paperclip mr-1"></i> {{ __('Attachments') }}
                                        </button>
                                        <a href="{{ route('reports.show', $report) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-eye mr-1"></i> {{ __('Details') }}
                                        </a>
                                    </div>
                                    <a href="{{ route('reports.edit', $report) }}" class="btn btn-outline-warning btn-sm ml-1 mb-1">
                                        <i class="fas fa-edit mr-1"></i> {{ __('Edit') }}
                                    </a>
                                    <form method="POST" action="{{ route('reports.destroy', $report) }}" class="ml-1 mb-1" onsubmit="return confirm('{{ __('Are you sure you want to move this report to the trash?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash mr-1"></i> {{ __('Trash') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @push('modals')
                            <div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                {{ __('Attachments for report :id', ['id' => $report->id]) }}
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            @if ($report->files->isEmpty())
                                                <p class="text-muted mb-0">{{ __('No attachments uploaded for this report.') }}</p>
                                            @else
                                                <div class="list-group list-group-flush">
                                                    @foreach ($report->files as $file)
                                                        @php
                                                            $mime = $file->mime ?? '';
                                                            $size = max(0.01, round(($file->size ?? 0) / 1024 / 1024, 2));
                                                            $previewUrl = URL::temporarySignedRoute('reports.files.preview', now()->addMinutes(30), [$report, $file]);
                                                            $downloadUrl = URL::temporarySignedRoute('reports.files.show', now()->addMinutes(30), [$report, $file]);
                                                        @endphp
                                                        <div class="list-group-item">
                                                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                                                <div>
                                                                    <p class="mb-1 font-weight-bold">{{ $file->original_name }}</p>
                                                                    <p class="mb-0 text-xs text-muted">
                                                                        {{ $mime ?: __('Unknown type') }} • {{ number_format($size, 2) }} {{ __('MB') }}
                                                                    </p>
                                                                </div>
                                                                <div class="btn-group btn-group-sm" role="group">
                                                                    <a href="{{ $downloadUrl }}" class="btn btn-outline-primary">
                                                                        <i class="fas fa-download"></i> {{ __('Download') }}
                                                                    </a>
                                                                    <a href="{{ $previewUrl }}" target="_blank" rel="noopener" class="btn btn-outline-secondary">
                                                                        <i class="fas fa-external-link-alt"></i> {{ __('Open') }}
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
                                                                    <p class="mt-2 text-xs text-muted">
                                                                        {{ __('If the preview does not render correctly, use the open button to view it in a dedicated tab.') }}
                                                                    </p>
                                                                @else
                                                                    <p class="text-xs text-muted mb-0">
                                                                        {{ __('Preview unavailable for this file type. Use the buttons above to open or download the file.') }}
                                                                    </p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                            <a href="{{ route('reports.show', $report) }}" class="btn btn-primary">
                                                <i class="fas fa-eye mr-1"></i> {{ __('View full report') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endpush
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->can('view-all') ? 8 : 7 }}" class="text-center text-muted py-4">
                                {{ __('No reports match the selected filters.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $reports->links('pagination::bootstrap-4') }}
        </div>
    </div>

    @stack('modals')
</x-admin-layout>
