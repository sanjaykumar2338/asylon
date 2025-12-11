<x-admin-layout>
    <x-slot name="header">
        {{ __('Manage Prices') }}: {{ $plan->name }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('platform.plans.index') }}" class="btn btn-link px-0">
            <i class="fas fa-arrow-left mr-1"></i> {{ __('Back to plans') }}
        </a>
        <span class="badge badge-light border text-uppercase">{{ $plan->slug }}</span>
    </div>

    <form method="POST" action="{{ route('platform.plans.prices.update', $plan) }}">
        @csrf
        @method('PUT')

        <div class="card card-outline card-primary mb-4">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <i class="fas fa-tags mr-2"></i> {{ __('Existing Prices') }}
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
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

        <div class="card card-outline card-secondary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    <i class="fas fa-plus mr-2"></i> {{ __('Add Price (optional)') }}
                </h3>
                <small class="text-muted">{{ __('Leave blank to skip') }}</small>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="new_interval">{{ __('Interval') }}</label>
                        <select name="new_price[billing_interval]" id="new_interval" class="form-control @error('new_price.billing_interval') is-invalid @enderror">
                            <option value="">{{ __('Select interval') }}</option>
                            @foreach ($intervals as $value => $label)
                                <option value="{{ $value }}" @selected(old('new_price.billing_interval') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('new_price.billing_interval')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label class="d-block">{{ __('Early Adopter?') }}</label>
                        <input type="hidden" name="new_price[is_early_adopter]" value="0">
                        <div class="custom-control custom-switch">
                            <input type="checkbox"
                                class="custom-control-input"
                                id="new-early"
                                name="new_price[is_early_adopter]"
                                value="1"
                                {{ old('new_price.is_early_adopter', false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="new-early">{{ __('Early pricing') }}</label>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="new-stripe-id">{{ __('Stripe Price ID') }}</label>
                        <input type="text"
                            name="new_price[stripe_price_id]"
                            id="new-stripe-id"
                            class="form-control @error('new_price.stripe_price_id') is-invalid @enderror"
                            value="{{ old('new_price.stripe_price_id') }}"
                            placeholder="price_..."
                            autocomplete="off">
                        @error('new_price.stripe_price_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-2">
                        <label class="d-block">{{ __('Active?') }}</label>
                        <input type="hidden" name="new_price[is_active]" value="0">
                        <div class="custom-control custom-switch">
                            <input type="checkbox"
                                class="custom-control-input"
                                id="new-active"
                                name="new_price[is_active]"
                                value="1"
                                {{ old('new_price.is_active', false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="new-active">{{ __('Enable') }}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> {{ __('Save Changes') }}
                </button>
            </div>
        </div>
    </form>
</x-admin-layout>
