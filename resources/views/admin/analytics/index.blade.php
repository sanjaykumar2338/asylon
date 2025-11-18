<x-admin-layout>
    <x-slot name="header">
        {{ __('Analytics') }}
    </x-slot>

    <div class="card card-outline card-primary mb-4">
        <div class="card-body">
            <form method="GET" class="form-row">
                <div class="form-group col-md-3">
                    <label class="small text-uppercase text-muted">{{ __('Portal') }}</label>
                    <select name="portal" class="form-control">
                        <option value="">{{ __('All portals') }}</option>
                        <option value="student" @selected($filters['portal'] === 'student')>{{ __('Student only') }}</option>
                        <option value="employee" @selected($filters['portal'] === 'employee')>{{ __('HR/Employee only') }}</option>
                        <option value="general" @selected($filters['portal'] === 'general')>{{ __('General') }}</option>
                    </select>
                </div>
                @if (isset($orgOptions) && $orgOptions->count() > 1)
                    <div class="form-group col-md-3">
                        <label class="small text-uppercase text-muted">{{ __('Organization') }}</label>
                        <select name="org_id" class="form-control">
                            <option value="">{{ __('All visible') }}</option>
                            @foreach ($orgOptions as $org)
                                <option value="{{ $org->id }}" @selected($filters['org_id'] == $org->id)>{{ $org->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="form-group col-md-3">
                    <label class="small text-uppercase text-muted">{{ __('From date') }}</label>
                    <input type="date" name="from" value="{{ $filters['from'] }}" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label class="small text-uppercase text-muted">{{ __('To date') }}</label>
                    <input type="date" name="to" value="{{ $filters['to'] }}" class="form-control">
                </div>
                <div class="form-group col-md-12 text-right mb-0">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter mr-1"></i> {{ __('Apply filters') }}
                    </button>
                    <a href="{{ route('admin.analytics') }}" class="btn btn-outline-secondary">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <p class="text-muted text-uppercase mb-2">{{ __('Total reports') }}</p>
                    <h3 class="font-weight-bold mb-0">{{ number_format($total) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <p class="text-muted text-uppercase mb-2">{{ __('Urgent reports') }}</p>
                    <h3 class="font-weight-bold mb-1">{{ number_format($urgent) }}</h3>
                    <span class="text-muted">{{ $urgentPercent }}% {{ __('of total') }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <p class="text-muted text-uppercase mb-2">{{ __('Avg first response') }}</p>
                    <h3 class="font-weight-bold mb-1">
                        {{ $avgResponse !== null ? $avgResponse . ' ' . __('mins') : __('N/A') }}
                    </h3>
                    <span class="text-muted">{{ __('Time from submission to first response') }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <p class="text-muted text-uppercase mb-2">{{ __('Top category') }}</p>
                    @if ($byCategory->isNotEmpty())
                        <h3 class="font-weight-bold mb-1">
                            {{ $byCategory->first()->category }}
                        </h3>
                        <span class="text-muted">{{ $byCategory->first()->total }} {{ __('reports') }}</span>
                        @if ($bySubcategory->isNotEmpty())
                            <p class="mt-2 mb-0 text-muted small">
                                {{ __('Top subcategory') }}: {{ $bySubcategory->first()->subcategory }}
                                <span class="d-block">{{ __('Within') }} {{ $bySubcategory->first()->category }}</span>
                                <span class="d-block">{{ $bySubcategory->first()->total }} {{ __('reports') }}</span>
                            </p>
                        @endif
                    @else
                        <h3 class="font-weight-bold mb-1">{{ __('N/A') }}</h3>
                        <span class="text-muted">{{ __('No submissions yet') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card card-outline card-primary h-100">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-layer-group mr-2"></i> {{ __('Reports by type') }}
                    </h3>
                </div>
                <div class="card-body">
                    @if ($byType->isEmpty())
                        <p class="text-muted mb-0">{{ __('No submissions yet.') }}</p>
                    @else
                        @php $typeTotal = max($byType->sum('total'), 1); @endphp
                        <ul class="list-unstyled mb-0">
                            @foreach ($byType as $item)
                                <li class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="font-weight-medium text-capitalize">{{ $item->type ?? __('Unknown') }}</span>
                                    <span class="badge badge-light">{{ $item->total }}</span>
                                </li>
                                <div class="progress mb-2" style="height: 6px;">
                                    <div class="progress-bar bg-secondary" role="progressbar"
                                        style="width: {{ max(($item->total / $typeTotal) * 100, 5) }}%;"
                                        aria-valuenow="{{ $item->total }}" aria-valuemin="0" aria-valuemax="{{ $typeTotal }}">
                                    </div>
                                </div>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card card-outline card-primary h-100">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle mr-2"></i> {{ __('Reports by severity') }}
                    </h3>
                </div>
                <div class="card-body">
                    @if ($bySeverity->isEmpty())
                        <p class="text-muted mb-0">{{ __('No submissions yet.') }}</p>
                    @else
                        @php $severityTotal = max($bySeverity->sum('total'), 1); @endphp
                        <ul class="list-unstyled mb-0">
                            @foreach ($bySeverity as $item)
                                <li class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="font-weight-medium text-capitalize">{{ $item->severity ?? __('Unknown') }}</span>
                                    <span class="badge badge-light">{{ $item->total }}</span>
                                </li>
                                <div class="progress mb-2" style="height: 6px;">
                                    <div class="progress-bar bg-danger" role="progressbar"
                                        style="width: {{ max(($item->total / $severityTotal) * 100, 5) }}%;"
                                        aria-valuenow="{{ $item->total }}" aria-valuemin="0" aria-valuemax="{{ $severityTotal }}">
                                    </div>
                                </div>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card card-outline card-primary h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-university mr-2"></i> {{ __('Reports by organization') }}
                    </h3>
                    <span class="badge badge-light">{{ __('Filtered view') }}</span>
                </div>
                <div class="card-body">
                    @if ($byOrg->isEmpty())
                        <p class="text-muted mb-0">{{ __('No submissions yet.') }}</p>
                    @else
                        @php $orgMax = max($byOrg->max('total'), 1); @endphp
                        <div class="list-group list-group-flush">
                            @foreach ($byOrg as $item)
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $item->org->name ?? __('Unknown org') }}</strong>
                                        </div>
                                        <span class="badge badge-light">{{ $item->total }}</span>
                                    </div>
                                    <div class="progress mt-2" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar"
                                            style="width: {{ max(($item->total / $orgMax) * 100, 5) }}%;"
                                            aria-valuenow="{{ $item->total }}" aria-valuemin="0" aria-valuemax="{{ $orgMax }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-primary mt-4">
        <div class="card-header">
            <h3 class="card-title mb-0">
                <i class="fas fa-chart-pie mr-2"></i> {{ __('Top categories') }}
            </h3>
        </div>
        <div class="card-body">
            @if ($byCategory->isEmpty())
                <p class="text-muted mb-0">
                    {{ __('Submissions will populate this list once available.') }}
                </p>
            @else
                @php
                    $maxTotal = max($byCategory->max('total'), 1);
                @endphp
                <div class="list-group list-group-flush">
                    @foreach ($byCategory as $category)
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>{{ $category->category }}</span>
                                <span class="badge badge-light">{{ $category->total }}</span>
                            </div>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ max(($category->total / $maxTotal) * 100, 5) }}%;"
                                    aria-valuenow="{{ $category->total }}" aria-valuemin="0" aria-valuemax="{{ $maxTotal }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="card card-outline card-primary mt-4">
        <div class="card-header">
            <h3 class="card-title mb-0">
                <i class="fas fa-stream mr-2"></i> {{ __('Top subcategories') }}
            </h3>
        </div>
        <div class="card-body">
            @if ($bySubcategory->isEmpty())
                <p class="text-muted mb-0">
                    {{ __('Submissions will populate this list once available.') }}
                </p>
            @else
                @php
                    $maxSubcategoryTotal = max($bySubcategory->max('total'), 1);
                @endphp
                <div class="list-group list-group-flush">
                    @foreach ($bySubcategory as $item)
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $item->subcategory }}</strong>
                                    <div class="small text-muted">{{ $item->category }}</div>
                                </div>
                                <span class="badge badge-light">{{ $item->total }}</span>
                            </div>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-info" role="progressbar"
                                    style="width: {{ max(($item->total / $maxSubcategoryTotal) * 100, 5) }}%;"
                                    aria-valuenow="{{ $item->total }}" aria-valuemin="0" aria-valuemax="{{ $maxSubcategoryTotal }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
