<x-admin-layout>
    <x-slot name="header">
        {{ __('Plans & Pricing') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-layer-group mr-2"></i> {{ __('Plans') }}
            </h3>
            <span class="badge badge-info badge-pill px-3 py-2">
                {{ __('Total') }}: {{ $plans->count() }}
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
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
                                                <span class="badge badge-light border mr-2 text-capitalize">
                                                    {{ $price->billing_interval }}
                                                </span>
                                                <span class="badge badge-{{ $price->is_early_adopter ? 'info' : 'secondary' }} mr-2">
                                                    {{ $price->is_early_adopter ? __('Early') : __('Standard') }}
                                                </span>
                                                <span class="badge badge-{{ $price->is_active ? 'success' : 'secondary' }} mr-2">
                                                    {{ $price->is_active ? __('Active') : __('Disabled') }}
                                                </span>
                                                <span class="small text-monospace text-muted">
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
</x-admin-layout>
