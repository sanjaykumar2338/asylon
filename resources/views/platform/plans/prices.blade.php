<x-admin-layout>
    <x-slot name="header">
        {{ __('Manage Prices') }}: {{ $plan->name }}
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
                min-height: 130px;
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
        $priceCount = $plan->prices->count();
        $activePriceCount = $plan->prices->where('is_active', true)->count();
        $hasEarly = $plan->prices->where('is_early_adopter', true)->count();
    @endphp

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="hero-panel p-4 p-md-5 mb-4 shadow-sm">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="text-uppercase small opacity-75 mb-1">{{ __('Plan pricing') }}</div>
                    <h2 class="fw-semibold mb-2">{{ $plan->name }}</h2>
                    <p class="mb-0 opacity-75">{{ __('Update Stripe price IDs and availability for this plan.') }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('platform.plans.index') }}" class="btn btn-light text-primary fw-semibold">
                        <i class="fas fa-arrow-left me-2"></i>{{ __('Back to plans') }}
                    </a>
                    <a href="{{ route('platform.billing.revenue') }}" class="btn btn-outline-light">
                        <i class="fa-solid fa-chart-line me-2"></i>{{ __('Revenue dashboard') }}
                    </a>
                    <span class="badge bg-light text-dark align-self-center text-uppercase px-3 py-2">{{ $plan->slug }}</span>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-sm-4 col-xl-4">
                <div class="card stat-card shadow-sm bg-primary">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="stat-icon">
                                <i class="fa-solid fa-tags"></i>
                            </span>
                            <span class="fw-semibold">{{ __('Prices') }}</span>
                        </div>
                        <div>
                            <div class="display-6 fw-bold mb-1">{{ $priceCount }}</div>
                            <span class="opacity-75 small">{{ __('Total price entries') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-xl-4">
                <div class="card stat-card shadow-sm bg-success">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="stat-icon">
                                <i class="fa-solid fa-toggle-on"></i>
                            </span>
                            <span class="fw-semibold">{{ __('Active prices') }}</span>
                        </div>
                        <div>
                            <div class="display-6 fw-bold mb-1">{{ $activePriceCount }}</div>
                            <span class="opacity-75 small">{{ __('Enabled Stripe prices') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-xl-4">
                <div class="card stat-card shadow-sm bg-info">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="stat-icon">
                                <i class="fa-solid fa-seedling"></i>
                            </span>
                            <span class="fw-semibold">{{ __('Early adopter') }}</span>
                        </div>
                        <div>
                            <div class="display-6 fw-bold mb-1">{{ $hasEarly }}</div>
                            <span class="opacity-75 small">{{ __('Early-pricing entries') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('platform.plans.prices.update', $plan) }}">
            @csrf
            @method('PUT')

            <div class="card card-lite shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-0">
                                <i class="fas fa-tags mr-2"></i> {{ __('Existing Prices') }}
                            </h5>
                            <small class="text-muted">{{ __('Manage Stripe price IDs and activation state') }}</small>
                        </div>
                        <span class="badge bg-secondary text-dark">{{ $priceCount }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('Interval') }}</th>
                                    <th scope="col">{{ __('Early Adopter') }}</th>
                                    <th scope="col" style="width: 45%;">{{ __('Stripe Price ID') }}</th>
                                    <th scope="col" class="text-center">{{ __('Active') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($plan->prices as $price)
                                    <tr>
                                        <td class="text-capitalize align-middle">
                                            {{ $price->billing_interval }}
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge badge-{{ $price->is_early_adopter ? 'info' : 'secondary' }}">
                                                {{ $price->is_early_adopter ? __('Early') : __('Standard') }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <input type="text"
                                                name="prices[{{ $price->id }}][stripe_price_id]"
                                                class="form-control @error('prices.'.$price->id.'.stripe_price_id') is-invalid @enderror"
                                                value="{{ old('prices.'.$price->id.'.stripe_price_id', $price->stripe_price_id) }}"
                                                autocomplete="off"
                                                required>
                                            @error('prices.'.$price->id.'.stripe_price_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td class="text-center align-middle">
                                            <input type="hidden" name="prices[{{ $price->id }}][is_active]" value="0">
                                            <div class="custom-control custom-switch d-inline-block">
                                                <input type="checkbox"
                                                    class="custom-control-input"
                                                    id="price-active-{{ $price->id }}"
                                                    name="prices[{{ $price->id }}][is_active]"
                                                    value="1"
                                                    {{ old('prices.'.$price->id.'.is_active', $price->is_active) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="price-active-{{ $price->id }}"></label>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            {{ __('No prices configured yet.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card card-lite shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-0 d-flex align-items-center gap-2">
                                <i class="fas fa-plus"></i> {{ __('Add Price (optional)') }}
                            </h5>
                            <small class="text-muted">{{ __('Leave fields blank to skip') }}</small>
                        </div>
                        <span class="badge bg-light text-dark text-uppercase">{{ __('New entry') }}</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-lg-8">
                            <div class="p-3 border rounded-3 bg-white">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="new_interval" class="fw-semibold">{{ __('Interval') }}</label>
                                        <select name="new_price[billing_interval]" id="new_interval" class="form-control form-control-lg @error('new_price.billing_interval') is-invalid @enderror">
                                            <option value="">{{ __('Select interval') }}</option>
                                            @foreach ($intervals as $value => $label)
                                                <option value="{{ $value }}" @selected(old('new_price.billing_interval') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('new_price.billing_interval')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end">
                                        <div class="w-100">
                                            <label class="fw-semibold d-block">{{ __('Early Adopter?') }}</label>
                                            <input type="hidden" name="new_price[is_early_adopter]" value="0">
                                            <div class="custom-control custom-switch custom-switch-lg d-inline-flex align-items-center" style="gap: 10px;">
                                                <input type="checkbox"
                                                    class="custom-control-input"
                                                    id="new-early"
                                                    name="new_price[is_early_adopter]"
                                                    value="1"
                                                    {{ old('new_price.is_early_adopter', false) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="new-early">{{ __('Early pricing') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="new-stripe-id" class="fw-semibold">{{ __('Stripe Price ID') }}</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text text-muted">price_</span>
                                            <input type="text"
                                                name="new_price[stripe_price_id]"
                                                id="new-stripe-id"
                                                class="form-control @error('new_price.stripe_price_id') is-invalid @enderror"
                                                value="{{ old('new_price.stripe_price_id') }}"
                                                placeholder="abc123..."
                                                autocomplete="off">
                                        </div>
                                        <small class="text-muted">{{ __('Copy the Price ID from Stripe’s dashboard.') }}</small>
                                        @error('new_price.stripe_price_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-semibold d-block">{{ __('Active?') }}</label>
                                        <input type="hidden" name="new_price[is_active]" value="0">
                                        <div class="custom-control custom-switch custom-switch-lg d-inline-flex align-items-center" style="gap: 10px;">
                                            <input type="checkbox"
                                                class="custom-control-input"
                                                id="new-active"
                                                name="new_price[is_active]"
                                                value="1"
                                                {{ old('new_price.is_active', false) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="new-active">{{ __('Enable') }}</label>
                                        </div>
                                        <small class="text-muted">{{ __('Toggle on to make this price selectable immediately.') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="h-100 p-3 border rounded-3 bg-light d-flex flex-column gap-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-primary">Tip</span>
                                        <strong>{{ __('Monthly vs yearly') }}</strong>
                                    </div>
                                    <p class="mb-0 text-muted small">{{ __('Add separate Stripe Price IDs for monthly and yearly billing to give customers both choices.') }}</p>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-success">Pro</span>
                                        <strong>{{ __('Early adopter') }}</strong>
                                    </div>
                                    <p class="mb-0 text-muted small">{{ __('Use early pricing for legacy discounts or launch promos while keeping standard pricing intact.') }}</p>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-warning text-dark">Reminder</span>
                                        <strong>{{ __('Keep IDs synced') }}</strong>
                                    </div>
                                    <p class="mb-0 text-muted small">{{ __('Ensure the Stripe Price ID matches the correct product and interval to avoid checkout errors.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save mr-2"></i> {{ __('Save Changes') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-admin-layout>
