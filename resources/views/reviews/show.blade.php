<x-admin-layout>
    <x-slot name="header">
        {{ __('Report Details') }}
    </x-slot>

    @push('styles')
        <style>
            .safety-blurred {
                filter: blur(14px);
                pointer-events: none;
                user-select: none;
            }
            .safety-overlay {
                position: absolute;
                inset: 0;
                background: rgba(0, 0, 0, 0.55);
                color: #fff;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                text-align: center;
                padding: 1rem;
                border-radius: .35rem;
            }
        </style>
    @endpush

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
                        @if ($report->escalationEvents->isNotEmpty()) 
                            <span class="badge badge-danger ml-2">{{ __('Escalated') }}</span> 
                        @endif 
                        @if ($report->riskAnalysis) 
                            @php 
                                $riskLevel = $report->riskAnalysis->risk_level; 
                                $riskClass = match ($riskLevel) {
                                    'high' => 'badge-danger',
                                    'medium' => 'badge-warning text-dark',
                                    default => 'badge-success',
                                };
                            @endphp
                            <span class="badge {{ $riskClass }} ml-2">
                                {{ __('Risk') }}: {{ ucfirst($riskLevel) }}
                                @if ($report->riskAnalysis->risk_score !== null)
                                    <span class="ml-1">({{ $report->riskAnalysis->risk_score }})</span>
                                @endif
                            </span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
                        <span class="text-monospace text-muted mr-3">#{{ $report->id }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if ($isUltraPrivate ?? false)
                        <div class="alert alert-warning d-flex align-items-start">
                            <i class="fas fa-user-secret mr-2 mt-1"></i>
                            <div>
                                <strong>{{ __('Ultra-private mode enabled') }}</strong>
                                <div class="small mb-0">
                                    {{ __('Reporter IP, user-agent, and location headers were scrubbed on intake. Only a subpoena-safe hash was retained for lawful requests.') }}
                                </div>
                                @if ($subpoenaToken)
                                    <div class="small text-monospace mt-1">
                                        {{ __('Subpoena token') }}: {{ $subpoenaToken }}
                                    </div>
                                @elseif (auth()->user()?->hasRole('platform_admin'))
                                    <div class="small text-muted mt-1">
                                        {{ __('No subpoena token was captured for this submission.') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

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
                            <a href="{{ route('followup.show', $report->chat_token) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-comments mr-1"></i> {{ __('Open chat') }}
                            </a>
                        </div>
                    </div> 

                    <div class="mt-4"> 
                        @if ($report->escalationEvents->isNotEmpty())
                            <h5 class="mb-3">{{ __('Escalation') }}</h5>
                            <div class="list-group mb-4">
                                @foreach ($report->escalationEvents as $event)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $event->rule_name ?? __('Escalation rule') }}</strong>
                                                <div class="small text-muted">
                                                    {{ $event->created_at?->format('M d, Y H:i') }}
                                                </div>
                                            </div>
                                            <span class="badge badge-danger">{{ __('Escalated') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

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

                    <div class="mt-3 d-flex flex-wrap justify-content-end">
                        @can('manage-data-requests')
                            <form method="POST" action="{{ route('admin.data_requests.from_case', $report) }}" class="mb-2 ml-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-eraser mr-1"></i> {{ __('Create Data Deletion Request') }}
                                </button>
                            </form>
                        @endcan
                        <a href="{{ route('reports.edit', $report) }}" class="btn btn-primary btn-sm mb-2 ml-2">
                            <i class="fas fa-edit mr-1"></i> {{ __('Edit') }}
                        </a>
                        <form method="POST" action="{{ route('reports.destroy', $report) }}"
                            class="mb-2 ml-2"
                            data-swal-confirm
                            data-swal-title="{{ __('Delete report') }}"
                            data-swal-message="{{ __('Are you sure you want to delete this report? This action cannot be undone.') }}"
                            data-swal-confirm-button="{{ __('Yes, delete') }}"
                            data-swal-icon="warning">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash mr-1"></i> {{ __('Delete') }}
                            </button>
                        </form>
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
                                        <h6 class="mb-1 {{ $message->side === 'reviewer' ? 'text-primary' : '' }}">
                                            {{ ucfirst($message->side) }}
                                        </h6>
                                        <small class="text-muted">
                                            <i class="far fa-clock mr-1"></i>{{ $message->sent_at?->format('M d, Y H:i') }}
                                        </small>
                                    </div>
                                    <p class="mb-0" style="white-space: pre-line;">{{ $message->message }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('reports.message', $report) }}">
                        @csrf
                        <div class="form-group">
                            <label for="message">{{ __('Reply to reporter') }}</label>
                            <textarea id="message" name="message" rows="4" required minlength="2" maxlength="5000"
                                class="form-control @error('message') is-invalid @enderror"
                                placeholder="{{ __('Write a reply to the reporter...') }}">{{ old('message') }}</textarea>
                            @error('message')
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

            <div class="card card-outline card-primary mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-stream mr-2"></i> {{ __('Activity History') }}
                    </h3>
                </div>
                <div class="card-body">
                    @if ($timeline->isEmpty())
                        <p class="text-muted mb-0">{{ __('No activity logged yet.') }}</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($timeline as $entry)
                                <li class="list-group-item px-0">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div class="d-flex align-items-start">
                                            <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mr-3" style="width: 34px; height: 34px;">
                                                <i class="{{ $entry['icon'] }}"></i>
                                            </span>
                                            <div>
                                                <div class="font-weight-bold">{{ $entry['title'] }}</div>
                                                <div class="text-muted small">{{ $entry['description'] }}</div>
                                            </div>
                                        </div>
                                        <div class="text-right text-muted small">
                                            {{ optional($entry['time'])->format('M d, Y H:i') }}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="card card-outline card-primary mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-sticky-note mr-2"></i> {{ __('Reviewer notes') }}
                    </h3>
                    <span class="badge badge-light text-muted">{{ __('Private') }}</span>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        {{ __('Use notes to coordinate internally. These are never shown to reporters.') }}
                    </p>
                    <div class="mb-4">
                        @forelse ($notes as $note)
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="font-weight-bold">
                                        {{ $note->user?->name ?? __('Reviewer') }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $note->created_at?->format('M d, Y H:i') }}
                                    </div>
                                </div>
                                <p class="mb-0" style="white-space: pre-line;">{{ $note->body }}</p>
                            </div>
                        @empty
                            <p class="text-muted mb-0">{{ __('No notes added yet.') }}</p>
                        @endforelse
                    </div>
                    <form method="POST" action="{{ route('reports.notes.store', $report) }}">
                        @csrf
                        <div class="form-group">
                            <label for="note_body">{{ __('Add a note') }}</label>
                            <textarea id="note_body" name="body" rows="3" maxlength="3000"
                                class="form-control @error('body') is-invalid @enderror"
                                placeholder="{{ __('Summarize reviewer actions or plans...') }}">{{ old('body') }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus mr-1"></i> {{ __('Add note') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-outline card-primary mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-robot mr-2"></i> {{ __('Threat Assessment (AI)') }}
                    </h3>
                    @php
                        $ta = $threatAssessment;
                        $level = $ta->level ?? 'low';
                        $levelBadge = match ($level) {
                            'low' => 'badge-success',
                            'moderate' => 'badge-warning text-dark',
                            'elevated' => 'badge-warning text-dark',
                            'high' => 'badge-danger',
                            'critical' => 'badge-dark',
                            default => 'badge-secondary',
                        };
                        $levelLabel = ucfirst($level);
                    @endphp
                    @if($ta)
                        <span class="badge {{ $levelBadge }}">{{ $levelLabel }}</span>
                    @endif
                </div>
                <div class="card-body">
                    @if (!$ta)
                        <p class="text-muted mb-0">{{ __('Threat assessment is not available yet.') }}</p>
                    @else
                        <div class="d-flex align-items-center mb-2">
                            <span class="h4 mb-0 mr-2">{{ $ta->score }}</span>
                            <span class="text-muted">{{ __('Score') }}</span>
                            @if($ta->subject_of_concern)
                                <span class="badge badge-danger ml-3"><i class="fas fa-exclamation-triangle mr-1"></i>{{ __('Subject of Concern') }}</span>
                            @endif
                        </div>
                        <p class="mb-2 text-muted">
                            <strong>{{ __('Recommendation') }}:</strong> {{ $ta->recommendation ?? __('Monitor') }}
                        </p>
                        @if (!empty($ta->signals))
                            <p class="mb-2 text-muted">
                                <strong>{{ __('Signals') }}:</strong>
                                {{ implode(', ', (array) $ta->signals) }}
                            </p>
                        @endif
                        @if (!empty($ta->summary))
                            <p class="mb-0">{{ $ta->summary }}</p>
                        @endif
                    @endif
                </div>
            </div>

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
                        <div class="form-group">
                            <label for="note">{{ __('Status note') }}</label>
                            <textarea id="note" name="note" rows="2" class="form-control @error('note') is-invalid @enderror" maxlength="2000" placeholder="{{ __('Add context for this change (optional)') }}">{{ old('note', $report->status_note) }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @if (!empty($reviewers) && $reviewers->isNotEmpty())
                            <div class="form-group">
                                <label for="resolved_by">{{ __('Resolved by (optional)') }}</label>
                                <select id="resolved_by" name="resolved_by" class="form-control @error('resolved_by') is-invalid @enderror">
                                    <option value="">{{ __('Auto-use current reviewer or leave unset') }}</option>
                                    @foreach ($reviewers as $reviewer)
                                        <option value="{{ $reviewer->id }}" @selected(old('resolved_by', $report->resolved_by) == $reviewer->id)>
                                            {{ $reviewer->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    {{ __('Only applied when marking Closed. Reopening clears the resolver.') }}
                                </small>
                                @error('resolved_by')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                        <div class="alert alert-info d-flex align-items-center py-2 px-3">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span class="small">
                                {{ __('First reviewer reply sets the first response timestamp. Org admins can get an email when this happens.') }}
                            </span>
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
                                    $sensitive = ($file->has_sensitive_content ?? false) || in_array($file->safety_scan_status, ['pending', 'pending_review', 'flagged'], true);
                                @endphp
                                <li class="list-group-item" data-file-id="{{ $file->id }}" data-sensitive="{{ $sensitive ? '1' : '0' }}">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div class="mr-3">
                                            <i class="{{ $icon }} mr-2"></i>
                                            <span class="font-weight-bold">{{ $file->original_name }}</span>
                                            @if ($sensitive)
                                                <span class="badge badge-warning text-dark ml-2">{{ __('Sensitive') }}</span>
                                            @endif
                                            <small class="text-muted d-block">{{ $mime ?: __('Unknown type') }} ? {{ number_format($sizeMb, 2) }} {{ __('MB') }}</small>
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
                                        @if (method_exists($file, 'isAudio') && $file->isAudio())
                                            @if (!empty($file->transcript))
                                                <p class="mt-3 mb-1 text-sm text-muted">
                                                    <strong>{{ __('Transcript') }}:</strong>
                                                    {{ \Illuminate\Support\Str::limit($file->transcript, 220) }}
                                                </p>
                                                @if (!empty($file->safety_scan_reasons))
                                                    <p class="mb-1 text-sm text-muted">
                                                        <strong>{{ __('Flags') }}:</strong>
                                                        {{ implode('; ', (array) $file->safety_scan_reasons) }}
                                                    </p>
                                                @endif
                                            @elseif (($file->transcription_status ?? '') === 'pending')
                                                <p class="mt-3 text-sm text-muted">{{ __('Transcription pending...') }}</p>
                                            @elseif (($file->transcription_status ?? '') === 'failed')
                                                <p class="mt-3 text-sm text-muted">{{ __('Transcription unavailable.') }}</p>
                                            @endif
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function initSensitivePreviews(root) {
                const items = root.querySelectorAll('[data-sensitive="1"]');
                items.forEach(function (item) {
                    const media = item.querySelector('img, video, audio, iframe');
                    if (!media) {
                        return;
                    }
                    const holder = media.parentElement;
                    holder.classList.add('position-relative');
                    media.classList.add('safety-blurred');

                    const fileId = item.getAttribute('data-file-id') || Math.random().toString(36).slice(2);
                    media.setAttribute('data-sensitive-content', fileId);

                    const overlay = document.createElement('div');
                    overlay.className = 'safety-overlay';
                    overlay.setAttribute('data-sensitive-overlay', fileId);
                    overlay.innerHTML = `
                        <p class="font-weight-bold mb-2">{{ __('Sensitive preview') }}</p>
                        <label class="d-block mb-2">
                            <input type="checkbox" data-sensitive-checkbox="${fileId}"> {{ __('I understand this may contain nudity/graphic content') }}
                        </label>
                        <button type="button" class="btn btn-outline-light btn-sm" data-sensitive-toggle="${fileId}">
                            {{ __('Reveal preview') }}
                        </button>
                    `;
                    holder.appendChild(overlay);
                });
            }

            initSensitivePreviews(document);

            document.body.addEventListener('click', function (event) {
                const toggle = event.target.closest('[data-sensitive-toggle]');
                if (!toggle) return;
                const id = toggle.getAttribute('data-sensitive-toggle');
                const checkbox = document.querySelector(`[data-sensitive-checkbox="${id}"]`);
                if (checkbox && !checkbox.checked) {
                    checkbox.focus();
                    return;
                }
                const content = document.querySelector(`[data-sensitive-content="${id}"]`);
                const overlay = document.querySelector(`[data-sensitive-overlay="${id}"]`);
                content?.classList.remove('safety-blurred');
                overlay?.classList.add('d-none');
            });
        });
    </script>
@endpush
