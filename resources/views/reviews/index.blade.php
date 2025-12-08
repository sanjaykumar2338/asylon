<x-admin-layout>
    <x-slot name="header">
        {{ __('Review Queue') }}
    </x-slot>

    @php
        $typeFilterOptions = [
            '' => __('All types'),
            'safety' => __('Safety & Threat'),
            'commendation' => __('Commendation'),
            'hr' => __('HR Anonymous'),
        ];
        $severityFilterOptions = [
            '' => __('All severities'),
            'low' => __('Low'),
            'moderate' => __('Moderate'),
            'high' => __('High'),
            'critical' => __('Critical'),
        ];
        $severityBadgeClasses = [
            'low' => 'badge-success',
            'moderate' => 'badge-warning text-dark',
            'high' => 'badge-danger',
            'critical' => 'badge-dark',
        ];
    @endphp
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
                        <label for="type">{{ __('Type') }}</label>
                        <select id="type" name="type" class="form-control">
                            @foreach ($typeFilterOptions as $value => $label)
                                <option value="{{ $value }}" @selected($type === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="severity">{{ __('Severity') }}</label>
                        <select id="severity" name="severity" class="form-control">
                            @foreach ($severityFilterOptions as $value => $label)
                                <option value="{{ $value }}" @selected($severity === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="risk_level">{{ __('Risk level') }}</label>
                        <select id="risk_level" name="risk_level" class="form-control">
                            <option value="">{{ __('All risk levels') }}</option>
                            <option value="high" @selected($riskLevel === 'high')>{{ __('High') }}</option>
                            <option value="medium" @selected($riskLevel === 'medium')>{{ __('Medium') }}</option>
                            <option value="low" @selected($riskLevel === 'low')>{{ __('Low') }}</option>
                            <option value="critical" @selected($riskLevel === 'critical')>{{ __('Critical') }}</option>
                            <option value="unscored" @selected($riskLevel === 'unscored')>{{ __('Unscored') }}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="category">{{ __('Category') }}</label>
                        <select id="category" name="category" class="form-control">
                            <option value="">{{ __('Any category') }}</option>
                            @foreach ($categoriesMap as $categoryName => $subcategories)
                                <option value="{{ $categoryName }}" @selected($category === $categoryName)>{{ $categoryName }}</option>
                            @endforeach
                            @if ($category !== '' && ! array_key_exists($category, $categoriesMap))
                                <option value="{{ $category }}" selected>{{ $category }} ({{ __('archived') }})</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="subcategory">{{ __('Subcategory') }}</label>
                        <select id="subcategory" name="subcategory" class="form-control" {{ $category === '' ? 'disabled' : '' }}>
                            <option value="">{{ __('Any subcategory') }}</option>
                            @foreach ($subcategoryOptions as $option)
                                <option value="{{ $option }}" @selected($subcategory === $option)>{{ $option }}</option>
                            @endforeach
                            @if ($subcategory !== '' && ! in_array($subcategory, $subcategoryOptions, true))
                                <option value="{{ $subcategory }}" selected>{{ $subcategory }} ({{ __('archived') }})</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="from">{{ __('Submitted From') }}</label>
                        <input type="date" id="from" name="from" value="{{ $from }}" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="to">{{ __('Submitted To') }}</label>
                        <input type="date" id="to" name="to" value="{{ $to }}" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="sort">{{ __('Sort by') }}</label>
                        <select id="sort" name="sort" class="form-control">
                            <option value="submitted_desc" @selected($sort === 'submitted_desc')>{{ __('Submitted date (newest first)') }}</option>
                            <option value="submitted_asc" @selected($sort === 'submitted_asc')>{{ __('Submitted date (oldest first)') }}</option>
                            <option value="violation_desc" @selected($sort === 'violation_desc')>{{ __('Violation date (newest first)') }}</option>
                            <option value="violation_asc" @selected($sort === 'violation_asc')>{{ __('Violation date (oldest first)') }}</option>
                            <option value="category_asc" @selected($sort === 'category_asc')>{{ __('Category A→Z') }}</option>
                            <option value="category_desc" @selected($sort === 'category_desc')>{{ __('Category Z→A') }}</option>
                            <option value="subcategory_asc" @selected($sort === 'subcategory_asc')>{{ __('Subcategory A→Z') }}</option>
                            <option value="subcategory_desc" @selected($sort === 'subcategory_desc')>{{ __('Subcategory Z→A') }}</option>
                        </select>
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
            <div class="d-flex flex-column flex-lg-row align-items-lg-center">
                <h3 class="card-title mb-0">
                    <i class="fas fa-clipboard-list mr-2"></i> {{ __('reports.title') }}
                </h3>
                <a href="{{ route('reviews.trash') }}" class="btn btn-outline-secondary btn-sm mt-3 mt-lg-0 ml-lg-3">
                    <i class="fas fa-trash-alt mr-1"></i> {{ __('View trash') }}
                </a>
            </div>
            <div class="d-flex flex-column flex-lg-row align-items-lg-center mt-3 mt-lg-0">
                <div class="btn-group mr-lg-3 mb-2 mb-lg-0" role="group" aria-label="Export reports">
                    <a href="{{ route('reports.export.list', request()->query()) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-file-csv mr-1"></i> {{ __('Export CSV') }}
                    </a>
                    <a href="{{ route('reports.export.list.pdf', request()->query()) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-file-pdf mr-1"></i> {{ __('Export PDF') }}
                    </a>
                </div>
                <span class="badge badge-info badge-pill px-3 py-2 ml-lg-2">
                    {{ __('Results') }}: {{ number_format($reports->total()) }}
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">#</th>
                        @can('view-all')
                            <th scope="col">{{ __('Organization') }}</th>
                        @endcan
                        <th scope="col">{{ __('Category / Subcategory') }}</th>
                        <th scope="col">{{ __('Type') }}</th>
                        <th scope="col">{{ __('Severity') }}</th>
                        <th scope="col">{{ __('Risk') }}</th>
                        <th scope="col">{{ __('Escalation') }}</th>
                        <th scope="col">{{ __('Urgent') }}</th>
                        <th scope="col">{{ __('Status') }}</th>
                        <th scope="col">{{ __('Attachments') }}</th>
                        <th scope="col">{{ __('Violation date') }}</th>
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
                            <td class="font-weight-bold">
                                {{ $report->category }}
                                @if ($report->subcategory)
                                    <div class="small text-muted">{{ $report->subcategory }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-light text-capitalize">{{ $report->type_label }}</span>
                            </td>
                            <td>
                                @php
                                    $severityClass = $severityBadgeClasses[$report->severity] ?? 'badge-secondary';
                                @endphp
                                <span class="badge {{ $severityClass }} text-capitalize">{{ $report->severity_label }}</span>
                            </td>
                            <td>
                                @php
                                    $riskLevel = $report->riskAnalysis->risk_level ?? null;
                                    $riskScore = $report->riskAnalysis->risk_score ?? null;
                                    $riskClass = match ($riskLevel) {
                                        'high' => 'badge-danger',
                                        'medium' => 'badge-warning text-dark',
                                        'low' => 'badge-success',
                                        default => 'badge-secondary',
                                    };
                                @endphp
                                @if ($riskLevel)
                                    <span class="badge {{ $riskClass }}">
                                        {{ ucfirst($riskLevel) }}
                                        @if ($riskScore !== null)
                                            <span class="ml-1">({{ $riskScore }})</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                            <td>
                                @if ($report->escalationEvents->isNotEmpty())
                                    <span class="badge badge-danger">{{ __('Escalated') }}</span>
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
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
                            <td>
                                @if ($report->violation_date)
                                    {{ $report->violation_date->format('M d, Y') }}
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
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
                                    <form method="POST" action="{{ route('reports.destroy', $report) }}" class="ml-1 mb-1"
                                        data-swal-confirm
                                        data-swal-title="{{ __('Move to trash') }}"
                                        data-swal-message="{{ __('Are you sure you want to move this report to the trash?') }}"
                                        data-swal-confirm-button="{{ __('Yes, move') }}"
                                        data-swal-icon="warning">
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
                                                                        {{ $mime ?: __('Unknown type') }} - {{ number_format($size, 2) }} {{ __('MB') }}
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
                            <td colspan="{{ auth()->user()->can('view-all') ? 11 : 10 }}" class="text-center text-muted py-4">
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const categoriesMap = @json($categoriesMap);
                const categorySelect = document.getElementById('category');
                const subcategorySelect = document.getElementById('subcategory');
                const placeholder = @json(__('Any subcategory'));
                const initialCategory = @json($category);
                const initialSubcategory = @json($subcategory);
                const archivedLabel = @json(__('archived'));

                function renderOptions(selectedCategory, chosenSubcategory = '') {
                    if (!subcategorySelect) {
                        return;
                    }

                    subcategorySelect.innerHTML = '';

                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = placeholder;
                    subcategorySelect.appendChild(defaultOption);

                    const options = categoriesMap[selectedCategory];
                    if (!selectedCategory || !Array.isArray(options)) {
                        if (selectedCategory && selectedCategory === initialCategory && initialSubcategory) {
                            const archivedOption = document.createElement('option');
                            archivedOption.value = initialSubcategory;
                            archivedOption.textContent = `${initialSubcategory} (${archivedLabel})`;
                            archivedOption.selected = true;
                            subcategorySelect.appendChild(archivedOption);
                        }
                        subcategorySelect.value = '';
                        subcategorySelect.disabled = true;
                        return;
                    }

                    options.forEach(function (item) {
                        const option = document.createElement('option');
                        option.value = item;
                        option.textContent = item;
                        subcategorySelect.appendChild(option);
                    });

                    subcategorySelect.disabled = false;

                    if (chosenSubcategory && options.includes(chosenSubcategory)) {
                        subcategorySelect.value = chosenSubcategory;
                    } else if (chosenSubcategory) {
                        const archivedOption = document.createElement('option');
                        archivedOption.value = chosenSubcategory;
                        archivedOption.textContent = `${chosenSubcategory} (${archivedLabel})`;
                        archivedOption.selected = true;
                        subcategorySelect.appendChild(archivedOption);
                    } else {
                        subcategorySelect.value = '';
                    }
                }

                renderOptions(categorySelect?.value, @json($subcategory));

                categorySelect?.addEventListener('change', function (event) {
                    renderOptions(event.target.value);
                });
            });
        </script>
    @endpush
</x-admin-layout>




