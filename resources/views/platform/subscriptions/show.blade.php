<x-admin-layout>
    <x-slot name="header">
        {{ __('Subscription:') }} {{ $org->name }}
    </x-slot>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <style>
            .hero-panel {
                background: linear-gradient(135deg, #0b1f3b, #173f74);
                color: #fff;
                border-radius: 18px;
            }
            .card-lite {
                border-radius: 14px;
                border: 1px solid #e5e7eb;
            }
        </style>
    @endpush

    @include('admin.partials.flash')

    @php
        $latest = $org->latestBillingSubscription;
        $statusBadge = $org->billing_status === 'active' ? 'success' : ($org->billing_status === 'past_due' ? 'warning' : 'secondary');
    @endphp

    <div class="container-fluid">
        <div class="hero-panel p-4 p-md-5 mb-4 shadow-sm">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="text-uppercase small opacity-75 mb-1">{{ __('Subscription overview') }}</div>
                    <h2 class="fw-semibold mb-2">{{ $org->name }}</h2>
                    <p class="mb-0 opacity-75">{{ __('Stripe subscription, billing status, and plan controls.') }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-{{ $statusBadge }} text-uppercase px-3 py-2">{{ ucfirst($org->billing_status ?? 'pending') }}</span>
                    <span class="badge bg-light text-dark px-3 py-2">{{ strtoupper($org->plan?->slug ?? $latest?->plan_slug ?? '—') }}</span>
                    @if ($latest?->current_period_end)
                        <span class="badge bg-warning text-dark px-3 py-2">{{ __('Renews') }} {{ $latest->current_period_end->format('M j, Y') }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="card card-lite shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">{{ __('Subscription Summary') }}</h5>
                            <span class="badge bg-{{ $statusBadge }}">{{ ucfirst($org->billing_status ?? 'pending') }}</span>
                        </div>
                        <dl class="row mb-0">
                            <dt class="col-sm-5">{{ __('Plan') }}</dt>
                            <dd class="col-sm-7 text-uppercase">{{ $org->plan?->name ?? $latest?->plan_slug ?? '—' }}</dd>

                            <dt class="col-sm-5">{{ __('Stripe Customer') }}</dt>
                            <dd class="col-sm-7"><code>{{ $org->stripe_customer_id ?? '—' }}</code></dd>

                            <dt class="col-sm-5">{{ __('Stripe Subscription') }}</dt>
                            <dd class="col-sm-7"><code>{{ $org->stripe_subscription_id ?? '—' }}</code></dd>

                            <dt class="col-sm-5">{{ __('Renewal / Current Period End') }}</dt>
                            <dd class="col-sm-7">{{ optional($latest?->current_period_end)->format('M j, Y') ?? '—' }}</dd>

                            <dt class="col-sm-5">{{ __('Cancel at period end') }}</dt>
                            <dd class="col-sm-7">
                                @if ($latest?->cancel_at_period_end)
                                    <span class="badge bg-warning text-dark">{{ __('Yes') }}</span>
                                @else
                                    <span class="badge bg-success">{{ __('No') }}</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-lite shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">{{ __('Actions') }}</h5>
                            <span class="badge bg-light text-dark">{{ __('Stripe sync & overrides') }}</span>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <form method="POST" action="{{ route('platform.billing.subscriptions.sync', $org) }}">
                                @csrf
                                <button class="btn btn-outline-secondary w-100" type="submit">
                                    <i class="fas fa-sync mr-1"></i>{{ __('Sync from Stripe') }}
                                </button>
                            </form>
                            <div class="d-flex gap-2">
                                <form method="POST" action="{{ route('platform.billing.subscriptions.cancel', $org) }}" class="flex-fill">
                                    @csrf
                                    <button class="btn btn-outline-danger w-100" type="submit">{{ __('Cancel at period end') }}</button>
                                </form>
                                <form method="POST" action="{{ route('platform.billing.subscriptions.resume', $org) }}" class="flex-fill">
                                    @csrf
                                    <button class="btn btn-outline-success w-100" type="submit">{{ __('Resume') }}</button>
                                </form>
                            </div>
                        </div>

                        <hr>

                        <form method="POST" action="{{ route('platform.billing.subscriptions.plan', $org) }}" class="row g-3">
                            @csrf
                            <div class="col-md-6">
                                <label for="plan_slug" class="fw-semibold">{{ __('Change Plan') }}</label>
                                <select name="plan_slug" id="plan_slug" class="form-control">
                                    @foreach ($plans as $plan)
                                        <option value="{{ $plan->slug }}">{{ $plan->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="interval" class="fw-semibold">{{ __('Interval') }}</label>
                                <select name="interval" id="interval" class="form-control">
                                    <option value="monthly">{{ __('Monthly') }}</option>
                                    <option value="yearly">{{ __('Yearly') }}</option>
                                </select>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">{{ __('Update Plan') }}</button>
                            </div>
                            <div class="col-12">
                                <p class="text-muted small mb-0">{{ __('APEX is custom/ manual; contact sales to modify.') }}</p>
                            </div>
                        </form>

                        <hr>

                        <form method="POST" action="{{ route('platform.billing.subscriptions.status', $org) }}" class="row g-3">
                            @csrf
                            <div class="col-md-8">
                                <label for="status" class="fw-semibold">{{ __('Manual Status Override') }}</label>
                                <select name="status" id="status" class="form-control">
                                    @foreach (['active', 'past_due', 'canceled', 'pending'] as $s)
                                        <option value="{{ $s }}" @selected($org->billing_status === $s)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end justify-content-end">
                                <button type="submit" class="btn btn-outline-secondary w-100">{{ __('Save Status') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card card-lite shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3">{{ __('Recent Invoices') }}</h5>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('Invoice') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Period') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($invoices as $invoice)
                                <tr>
                                    <td><code>{{ $invoice->stripe_invoice_id ?? '—' }}</code></td>
                                    <td>{{ strtoupper($invoice->currency ?? 'usd') }} {{ number_format(($invoice->amount ?? 0) / 100, 2) }}</td>
                                    <td><span class="badge badge-{{ ($invoice->status === 'paid') ? 'success' : 'secondary' }}">{{ ucfirst($invoice->status ?? 'unknown') }}</span></td>
                                    <td>
                                        @if ($invoice->period_start || $invoice->period_end)
                                            {{ optional($invoice->period_start)->format('M j') }} — {{ optional($invoice->period_end)->format('M j, Y') }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">{{ __('No invoices found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card card-lite shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3">{{ __('Recent Payments') }}</h5>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('Payment') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Paid at') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $payment)
                                <tr>
                                    <td><code>{{ $payment->stripe_payment_id ?? '—' }}</code></td>
                                    <td>{{ strtoupper($payment->currency ?? 'usd') }} {{ number_format(($payment->amount ?? 0) / 100, 2) }}</td>
                                    <td>{{ optional($payment->paid_at)->format('M j, Y') ?? '—' }}</td>
                                    <td><span class="badge badge-{{ ($payment->status === 'succeeded' || $payment->status === 'paid') ? 'success' : 'secondary' }}">{{ ucfirst($payment->status ?? 'unknown') }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">{{ __('No payments found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card card-lite shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">{{ __('Billing Events') }}</h5>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('When') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Old → New') }}</th>
                                <th>{{ __('Meta') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($events as $event)
                                <tr>
                                    <td>{{ $event->created_at->format('M j, Y H:i') }}</td>
                                    <td><span class="badge badge-secondary">{{ $event->type }}</span></td>
                                    <td>{{ $event->old_value ?? '—' }} → {{ $event->new_value ?? '—' }}</td>
                                    <td><code>{{ $event->meta ? json_encode($event->meta) : '—' }}</code></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">{{ __('No events logged yet.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
