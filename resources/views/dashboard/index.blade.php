<x-admin-layout>
    <x-slot name="header">
        {{ __('Reviewer Dashboard') }}
    </x-slot>

    <div class="card card-outline card-primary">
        <div class="card-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
            <h3 class="card-title mb-0">
                <i class="fas fa-clipboard-list mr-2"></i> {{ __('Latest Reports') }}
            </h3>
            <span class="badge badge-info badge-pill px-3 py-2 mt-3 mt-lg-0">
                {{ __('Total') }}: {{ number_format($reports->total()) }}
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{ __('Category') }}</th>
                            <th scope="col">{{ __('Urgent') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                            <th scope="col">{{ __('Attachments') }}</th>
                            <th scope="col">{{ __('Created') }}</th>
                            <th scope="col" class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reports as $report)
                            @php
                                $modalId = 'dashboard-attachments-' . \Illuminate\Support\Str::slug($report->getKey(), '-');
                            @endphp
                            <tr>
                                <td class="text-muted font-weight-bold">#{{ $report->id }}</td>
                                <td>{{ $report->category }}</td>
                                <td>
                                    @if ($report->urgent)
                                        <span class="badge badge-danger">{{ __('Yes') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('No') }}</span>
                                    @endif
                                </td>
                                <td class="text-capitalize">{{ str_replace('_', ' ', $report->status) }}</td>
                                <td>
                                    <span class="badge badge-light">
                                        <i class="fas fa-paperclip mr-1"></i> {{ $report->files_count }}
                                    </span>
                                </td>
                                <td>{{ $report->created_at->format('M d, Y H:i') }}</td>
                                <td class="text-right">
                                    <div class="d-flex flex-wrap justify-content-end">
                                        <a href="{{ route('reports.show', $report) }}"
                                            class="btn btn-outline-primary btn-sm mb-1 mr-1">
                                            <i class="fas fa-eye mr-1"></i> {{ __('View details') }}
                                        </a>
                                        <a href="#{{ $modalId }}" class="btn btn-outline-secondary btn-sm mb-1 mr-1"
                                            data-toggle="modal" data-target="#{{ $modalId }}">
                                            <i class="fas fa-paperclip mr-1"></i> {{ __('View attachments') }}
                                        </a>
                                        <form method="POST" action="{{ route('reports.destroy', $report) }}"
                                            class="mb-1"
                                            onsubmit="return confirm('{{ __('Are you sure you want to move this report to the trash?') }}');">
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
                                                <h5 class="modal-title">{{ __('Attachments for report :id', ['id' => $report->id]) }}</h5>
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
                                <td colspan="7" class="text-center text-muted py-4">
                                    {{ __('No reports found for your organization yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $reports->links('pagination::bootstrap-4') }}
        </div>
    </div>
    @stack('modals')
</x-admin-layout>
