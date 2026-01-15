<x-admin-layout>
    <x-slot name="header">
        {{ __('Revenue Dashboard') }}
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
                min-height: 170px;
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
        $formatMoney = static fn ($amount, $currency = 'usd') => strtoupper((string) $currency) . ' ' . number_format(($amount ?? 0) / 100, 2);
        $statusBadge = static function ($status): string {
            $map = [
                'paid' => 'success',
                'succeeded' => 'success',
                'active' => 'success',
                'processing' => 'info',
                'open' => 'info',
                'requires_payment_method' => 'warning',
                'past_due' => 'warning',
                'refunded' => 'secondary',
                'canceled' => 'secondary',
                'uncollectible' => 'danger',
            ];

            $key = strtolower((string) $status);

            return $map[$key] ?? 'secondary';
        };
    @endphp

    @include('admin.partials.flash')

    @php
        $isLive = $usingStripe && ($syncStatus['ok'] ?? true);
        $sourceLabel = $isLive ? __('Live (Stripe)') : __('Fallback (DB cached)');
        $sourceClass = $isLive ? 'success' : 'secondary';
        $paymentCount = count($payments);
        $invoiceCount = count($invoices);
        $refundCount = count($refunds);
    @endphp

    <div class="container-fluid">
        <div class="hero-panel p-4 p-md-5 mb-4 shadow-sm">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="text-uppercase small opacity-75 mb-1">{{ __('Platform revenue') }}</div>
                    <h2 class="fw-semibold mb-2">{{ $metrics['total_revenue'] }}</h2>
                    <p class="mb-0 opacity-75">{{ __('Live Stripe sync with cached fallback for resiliency.') }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('platform.billing.subscriptions.index') }}" class="btn btn-light text-primary fw-semibold">
                        <i class="fa-solid fa-file-invoice-dollar me-2"></i>{{ __('View subscriptions') }}
                    </a>
                    <a href="{{ route('platform.plans.index') }}" class="btn btn-outline-light">
                        <i class="fa-solid fa-credit-card me-2"></i>{{ __('Manage plans & prices') }}
                    </a>
                    <span class="badge bg-{{ $sourceClass }} align-self-center px-3 py-2">{{ $sourceLabel }}</span>
                </div>
            </div>
        </div>

        @if (! $usingStripe)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                {{ __('Stripe is not configured. Showing locally cached billing data only.') }}
            </div>
        @elseif (! ($syncStatus['ok'] ?? true))
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                {{ __('Stripe sync fell back to local records.') }}
                @if (! empty($syncStatus['error']))
                    <span class="ml-2 text-muted">{{ $syncStatus['error'] }}</span>
                @endif
            </div>
        @endif

        <div class="card admin-index-card card-outline card-primary mb-4">
            <div class="card-body">
                <form method="GET" class="admin-filter-bar mb-0">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="start_date">{{ __('Start date') }}</label>
                            <input type="date" name="start_date" id="start_date"
                                value="{{ optional($startDate)->toDateString() }}"
                                class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date">{{ __('End date') }}</label>
                            <input type="date" name="end_date" id="end_date"
                                value="{{ optional($endDate)->toDateString() }}"
                                class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="plan">{{ __('Plan') }}</label>
                            <select name="plan" id="plan" class="form-control">
                                <option value="">{{ __('All plans') }}</option>
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->slug }}" @selected($planSlug === $plan->slug)>
                                        {{ $plan->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="org_id">{{ __('Organization') }}</label>
                            <select name="org_id" id="org_id" class="form-control">
                                <option value="">{{ __('All organizations') }}</option>
                                @foreach ($organizations as $org)
                                    <option value="{{ $org->id }}" @selected($orgId === $org->id)>
                                        {{ $org->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 d-flex justify-content-end mt-3">
                            <a href="{{ route('platform.billing.revenue') }}" class="btn btn-outline-secondary mr-2">
                                <i class="fas fa-undo mr-1"></i>{{ __('Reset') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search mr-1"></i>{{ __('Apply filters') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card shadow-sm bg-primary">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="stat-icon">
                                <i class="fa-solid fa-coins"></i>
                            </span>
                            <span class="fw-semibold">{{ __('Total Revenue') }}</span>
                        </div>
                        <div>
                            <div class="display-6 fw-bold mb-1">{{ $metrics['total_revenue'] }}</div>
                            <span class="opacity-75 small">{{ __('Lifetime, respects filters') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card shadow-sm bg-success">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="stat-icon">
                                <i class="fa-solid fa-sync-alt"></i>
                            </span>
                            <span class="fw-semibold">{{ __('MRR') }}</span>
                        </div>
                        <div>
                            <div class="display-6 fw-bold mb-1">{{ $metrics['mrr'] }}</div>
                            <span class="opacity-75 small">{{ __('Monthly recurring revenue') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card shadow-sm bg-info">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="stat-icon">
                                <i class="fa-solid fa-calendar-alt"></i>
                            </span>
                            <span class="fw-semibold">{{ __('ARR') }}</span>
                        </div>
                        <div>
                            <div class="display-6 fw-bold mb-1">{{ $metrics['arr'] }}</div>
                            <span class="opacity-75 small">{{ __('Annual recurring revenue') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card shadow-sm bg-warning">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="stat-icon">
                                <i class="fa-solid fa-layer-group"></i>
                            </span>
                            <span class="fw-semibold">{{ __('Active Subs') }}</span>
                        </div>
                        <div>
                            <div class="display-6 fw-bold mb-1">{{ number_format($metrics['active_subscriptions']) }}</div>
                            <span class="opacity-75 small">{{ __('Stripe + local fallback') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card card-lite shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="mb-0">{{ __('Recent Payments') }}</h5>
                                <small class="text-muted">{{ __('Platform-level recent charges') }}</small>
                            </div>
                            <span class="badge bg-secondary text-dark">{{ $paymentCount }}</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Org') }}</th>
                                        <th>{{ __('Plan') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th>{{ __('Paid at') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->org->name ?? __('N/A') }}</td>
                                            <td class="text-uppercase">{{ $payment->plan_slug ?? '—' }}</td>
                                            <td>{{ $formatMoney($payment->amount, $payment->currency) }}</td>
                                            <td>{{ optional($payment->paid_at)->format('M j, Y') ?? '—' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $statusBadge($payment->status) }}">
                                                    {{ ucfirst($payment->status ?? __('unknown')) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">
                                                {{ __('No payments found for the selected range.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-lite shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="mb-0">{{ __('Invoices') }}</h5>
                                <small class="text-muted">{{ __('Latest invoice activity') }}</small>
                            </div>
                            <span class="badge bg-secondary text-dark">{{ $invoiceCount }}</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Invoice') }}</th>
                                        <th>{{ __('Org') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th>{{ __('Period') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($invoices as $invoice)
                                        <tr>
                                            <td><code>{{ $invoice->stripe_invoice_id ?? '—' }}</code></td>
                                            <td>{{ $invoice->org->name ?? __('N/A') }}</td>
                                            <td>{{ $formatMoney($invoice->amount, $invoice->currency) }}</td>
                                            <td>
                                                @if ($invoice->period_start || $invoice->period_end)
                                                    {{ optional($invoice->period_start)->format('M j') ?? '—' }}
                                                    &ndash;
                                                    {{ optional($invoice->period_end)->format('M j, Y') ?? '—' }}
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $statusBadge($invoice->status) }}">
                                                    {{ ucfirst($invoice->status ?? __('unknown')) }}
                                                </span>
                                                @if ($invoice->paid_at)
                                                    <small class="d-block text-muted">{{ __('Paid') }} {{ $invoice->paid_at->format('M j, Y') }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">
                                                {{ __('No invoices available for the selected filters.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-lite shadow-sm mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-0">{{ __('Refunds') }}</h5>
                        <small class="text-muted">{{ __('Historical refunds by org') }}</small>
                    </div>
                    <span class="badge bg-secondary text-dark">{{ $refundCount }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Org') }}</th>
                                <th>{{ __('Plan') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Reason') }}</th>
                                <th>{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($refunds as $refund)
                                <tr>
                                    <td>{{ $refund->org->name ?? __('N/A') }}</td>
                                    <td class="text-uppercase">{{ $refund->plan_slug ?? '—' }}</td>
                                    <td>{{ $formatMoney($refund->amount, $refund->currency) }}</td>
                                    <td>{{ $refund->reason ?? __('Not specified') }}</td>
                                    <td>{{ optional($refund->refunded_at)->format('M j, Y') ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">
                                        {{ __('No refunds recorded for the selected filters.') }}
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
