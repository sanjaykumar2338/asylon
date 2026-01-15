<x-admin-layout>
    <x-slot name="header">
        {{ __('Plans & Pricing') }}
    </x-slot>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <style>
            .hero-panel {
                background: linear-gradient(135deg, #0b1f3b, #173f74);
                color: #fff;
                border-radius: 18px;
            }
            .stat-card {
                border: 0;
                border-radius: 14px;
                color: #fff;
                min-height: 150px;
            }
            .stat-icon {
                height: 46px;
                width: 46px;
                border-radius: 12px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: rgba(255,255,255,0.18);
            }
            .card-lite {
                border-radius: 14px;
                border: 1px solid #e5e7eb;
            }
        </style>
    @endpush

    @php
        $totalPlans = $plans->count();
        $totalPrices = $plans->sum(fn ($plan) => $plan->prices->count());
        $activePrices = $plans->sum(fn ($plan) => $plan->prices->where('is_active', true)->count());
    @endphp

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="hero-panel p-4 p-md-5 mb-4 shadow-sm">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="text-uppercase small opacity-75 mb-1">{{ __('Platform plans') }}</div>
                    <h2 class="fw-semibold mb-2">{{ $totalPlans }} {{ __('plans configured') }}</h2>
                    <p class="mb-0 opacity-75">{{ __('Manage plan catalog and Stripe price IDs in one place.') }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('platform.billing.subscriptions.index') }}" class="btn btn-light text-primary fw-semibold">
                        <i class="fa-solid fa-file-invoice-dollar me-2"></i>{{ __('View subscriptions') }}
                    </a>
                    <a href="{{ route('platform.billing.revenue') }}" class="btn btn-outline-light">
                        <i class="fa-solid fa-chart-line me-2"></i>{{ __('Revenue dashboard') }}
                    </a>
                    <span class="badge bg-info align-self-center px-3 py-2">{{ __('Total prices') }}: {{ $totalPrices }}</span>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-sm-4 col-xl-4">
                <div class="card stat-card shadow-sm bg-primary">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="stat-icon">
                                <i class="fa-solid fa-layer-group"></i>
                            </span>
                            <span class="fw-semibold">{{ __('Plans') }}</span>
                        </div>
                        <div>
                            <div class="display-6 fw-bold mb-1">{{ $totalPlans }}</div>
                            <span class="opacity-75 small">{{ __('Active catalog entries') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-xl-4">
                <div class="card stat-card shadow-sm bg-success">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="stat-icon">
                                <i class="fa-solid fa-tags"></i>
                            </span>
                            <span class="fw-semibold">{{ __('Price entries') }}</span>
                        </div>
                        <div>
                            <div class="display-6 fw-bold mb-1">{{ $totalPrices }}</div>
                            <span class="opacity-75 small">{{ __('Stripe price IDs linked to plans') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-xl-4">
                <div class="card stat-card shadow-sm bg-info">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="stat-icon">
                                <i class="fa-solid fa-toggle-on"></i>
                            </span>
                            <span class="fw-semibold">{{ __('Active prices') }}</span>
                        </div>
                        <div>
                            <div class="display-6 fw-bold mb-1">{{ $activePrices }}</div>
                            <span class="opacity-75 small">{{ __('Enabled Stripe prices') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-lite shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-layer-group mr-2"></i> {{ __('Plans') }}
                        </h5>
                        <small class="text-muted">{{ __('Platform plans with price variants') }}</small>
                    </div>
                    <span class="badge bg-secondary text-dark">{{ $totalPlans }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">{{ __('Name') }}</th>
                                <th scope="col">{{ __('Slug') }}</th>
                                <th scope="col">{{ __('Prices') }}</th>
                                <th scope="col" class="text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($plans as $plan)
                                <tr>
                                    <td class="font-weight-bold">{{ $plan->name }}</td>
                                    <td><code>{{ $plan->slug }}</code></td>
                                    <td>
                                        @if ($plan->prices->isEmpty())
                                            <span class="text-muted">{{ __('No prices configured') }}</span>
                                        @else
                                            <div class="d-flex flex-column">
                                                @foreach ($plan->prices as $price)
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="badge badge-light text-dark border mr-2 text-capitalize">
                                                            {{ $price->billing_interval }}
                                                        </span>
                                                        <span class="badge badge-{{ $price->is_early_adopter ? 'info' : 'secondary' }} {{ $price->is_early_adopter ? 'text-dark' : '' }} mr-2">
                                                            {{ $price->is_early_adopter ? __('Early') : __('Standard') }}
                                                        </span>
                                                        <span class="badge badge-{{ $price->is_active ? 'success' : 'secondary' }} {{ $price->is_active ? '' : 'text-dark' }} mr-2">
                                                            {{ $price->is_active ? __('Active') : __('Disabled') }}
                                                        </span>
                                                        <span class="small text-monospace text-dark">
                                                            {{ \Illuminate\Support\Str::limit($price->stripe_price_id, 24) }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('platform.plans.prices.edit', $plan) }}" class="btn btn-outline-primary btn-sm">
                                            {{ __('Manage Prices') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        {{ __('No plans found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
