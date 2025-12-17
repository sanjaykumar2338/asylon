<x-admin-layout>
    <x-slot name="header">
        {{ __('Subscription:') }} {{ $org->name }}
    </x-slot>

    @include('admin.partials.flash')

    @php
        $latest = $org->latestBillingSubscription;
        $statusBadge = $org->billing_status === 'active' ? 'success' : ($org->billing_status === 'past_due' ? 'warning' : 'secondary');
    @endphp

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card card-outline card-primary h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">{{ __('Subscription Summary') }}</h3>
                    <span class="badge badge-{{ $statusBadge }}">{{ ucfirst($org->billing_status ?? 'pending') }}</span>
                </div>
                <div class="card-body">
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
                                <span class="badge badge-warning">{{ __('Yes') }}</span>
                            @else
                                <span class="badge badge-success">{{ __('No') }}</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card card-outline card-secondary h-100">
                <div class="card-header">
                    <h3 class="card-title mb-0">{{ __('Actions') }}</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('platform.billing.subscriptions.sync', $org) }}" class="mb-2">
                        @csrf
                        <button class="btn btn-outline-secondary btn-block" type="submit">
                            <i class="fas fa-sync mr-1"></i>{{ __('Sync from Stripe') }}
                        </button>
                    </form>

                    <div class="d-flex">
                        <form method="POST" action="{{ route('platform.billing.subscriptions.cancel', $org) }}" class="mr-2 flex-fill">
                            @csrf
                            <button class="btn btn-outline-danger btn-block" type="submit">{{ __('Cancel at period end') }}</button>
                        </form>
                        <form method="POST" action="{{ route('platform.billing.subscriptions.resume', $org) }}" class="flex-fill">
                            @csrf
                            <button class="btn btn-outline-success btn-block" type="submit">{{ __('Resume') }}</button>
                        </form>
                    </div>

                    <hr>

                    <form method="POST" action="{{ route('platform.billing.subscriptions.plan', $org) }}">
                        @csrf
                        <div class="form-group">
                            <label for="plan_slug">{{ __('Change Plan') }}</label>
                            <select name="plan_slug" id="plan_slug" class="form-control">
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->slug }}">{{ $plan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="interval">{{ __('Interval') }}</label>
                            <select name="interval" id="interval" class="form-control">
                                <option value="monthly">{{ __('Monthly') }}</option>
                                <option value="yearly">{{ __('Yearly') }}</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">{{ __('Update Plan') }}</button>
                        <p class="text-muted small mt-2 mb-0">{{ __('APEX is custom/ manual; contact sales to modify.') }}</p>
                    </form>

                    <hr>

                    <form method="POST" action="{{ route('platform.billing.subscriptions.status', $org) }}">
                        @csrf
                        <div class="form-group">
                            <label for="status">{{ __('Manual Status Override') }}</label>
                            <select name="status" id="status" class="form-control">
                                @foreach (['active', 'past_due', 'canceled', 'pending'] as $s)
                                    <option value="{{ $s }}" @selected($org->billing_status === $s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-outline-secondary btn-block">{{ __('Save Status') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-primary mb-4">
        <div class="card-header">
            <h3 class="card-title mb-0">{{ __('Recent Invoices') }}</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-striped mb-0">
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

    <div class="card card-outline card-secondary mb-4">
        <div class="card-header">
            <h3 class="card-title mb-0">{{ __('Recent Payments') }}</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-striped mb-0">
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

    <div class="card card-outline card-light">
        <div class="card-header">
            <h3 class="card-title mb-0">{{ __('Billing Events') }}</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-striped mb-0">
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
</x-admin-layout>
