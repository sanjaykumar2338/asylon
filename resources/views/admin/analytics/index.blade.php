<x-admin-layout>
    <x-slot name="header">
        {{ __('Analytics') }}
    </x-slot>

    <div class="row mb-4">
        <div class="col-md-6">
            <span class="badge badge-primary badge-pill px-3 py-2">
                <i class="fas fa-building mr-1"></i> {{ $orgLabel }}
            </span>
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
                    @else
                        <h3 class="font-weight-bold mb-1">{{ __('N/A') }}</h3>
                        <span class="text-muted">{{ __('No submissions yet') }}</span>
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
</x-admin-layout>
