<x-admin-layout>
    <x-slot name="header">
        {{ __('Revenue Dashboard') }}
    </x-slot>

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

    @php
        $isLive = $usingStripe && ($syncStatus['ok'] ?? true);
        $sourceLabel = $isLive ? __('Live (Stripe)') : __('Fallback (DB cached)');
        $sourceClass = $isLive ? 'success' : 'secondary';
    @endphp

    <div class="card card-outline card-primary mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h3 class="card-title mb-0 mr-3">
                    <i class="fas fa-filter mr-2"></i>{{ __('Filters') }}
                </h3>
                <span class="badge badge-{{ $sourceClass }} px-3 py-2">
                    {{ $sourceLabel }}
                </span>
            </div>
            <span class="text-muted small">
                {{ __('Data will fall back to local records if Stripe is unavailable.') }}
            </span>
        </div>
        <div class="card-body">
            <form method="GET" class="form-row">
                <div class="form-group col-md-3">
                    <label for="start_date">{{ __('Start date') }}</label>
                    <input type="date" name="start_date" id="start_date"
                        value="{{ optional($startDate)->toDateString() }}"
                        class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label for="end_date">{{ __('End date') }}</label>
                    <input type="date" name="end_date" id="end_date"
                        value="{{ optional($endDate)->toDateString() }}"
                        class="form-control">
                </div>
                <div class="form-group col-md-3">
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
                <div class="form-group col-md-3">
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
                <div class="col-12 d-flex justify-content-end">
                    <a href="{{ route('platform.billing.revenue') }}" class="btn btn-outline-secondary mr-2">
                        <i class="fas fa-undo mr-1"></i>{{ __('Reset') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i>{{ __('Apply filters') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $metrics['total_revenue'] }}</h3>
                    <p>{{ __('Total Revenue (lifetime)') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-coins"></i>
                </div>
                <span class="small-box-footer text-white-50">
                    {{ __('Includes filters when provided') }}
                </span>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $metrics['mrr'] }}</h3>
                    <p>{{ __('Monthly Recurring Revenue') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <span class="small-box-footer text-white-50">
                    {{ __('Cached for performance') }}
                </span>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $metrics['arr'] }}</h3>
                    <p>{{ __('Annual Recurring Revenue') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <span class="small-box-footer text-white-50">
                    {{ __('MRR × 12') }}
                </span>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($metrics['active_subscriptions']) }}</h3>
                    <p>{{ __('Active Subscriptions') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-stream"></i>
                </div>
                <span class="small-box-footer text-white-50">
                    {{ __('Stripe + local fallback') }}
                </span>
            </div>
        </div>
    </div>

    <div class="card card-outline card-primary mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-credit-card mr-2"></i>{{ __('Recent Payments') }}
            </h3>
            <span class="badge badge-light">{{ count($payments) }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-light">
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

    <div class="card card-outline card-secondary mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-file-invoice mr-2"></i>{{ __('Invoices') }}
            </h3>
            <span class="badge badge-light">{{ count($invoices) }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-light">
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

    <div class="card card-outline card-light">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-undo-alt mr-2"></i>{{ __('Refunds') }}
            </h3>
            <span class="badge badge-light">{{ count($refunds) }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-light">
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
</x-admin-layout>
