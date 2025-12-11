<x-admin-layout>
    <x-slot name="header">
        {{ __('Choose a Plan') }}
    </x-slot>

    @include('admin.partials.flash')

    @php
        $preferred = $org?->preferred_plan;
    @endphp

    @if ($preferred)
        <div class="alert alert-info d-flex justify-content-between align-items-center mb-4">
            <span>
                {{ __('You selected the :plan plan during signup.', ['plan' => strtoupper($preferred)]) }}
            </span>
            <span class="badge badge-light border text-uppercase">{{ $preferred }}</span>
        </div>
    @endif

    <div class="row">
        @foreach ($plans as $plan)
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm {{ $preferred === $plan->slug ? 'border-primary' : '' }}">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="card-title mb-0 text-uppercase">{{ $plan->name }}</h3>
                            <span class="badge badge-primary">{{ $plan->slug }}</span>
                        </div>
                        <p class="text-muted mb-3">
                            {{ $plan->slug === 'pro'
                                ? __('Full-featured for larger teams and districts.')
                                : __('Core protections to get your organization started quickly.') }}
                        </p>

                        <ul class="list-unstyled mb-4">
                            <li class="mb-1"><i class="fas fa-check text-success mr-2"></i>{{ __('Incident intake & triage') }}</li>
                            <li class="mb-1"><i class="fas fa-check text-success mr-2"></i>{{ __('Alerts & notifications') }}</li>
                            <li class="mb-1"><i class="fas fa-check text-success mr-2"></i>{{ __('Analytics dashboard') }}</li>
                            @if ($plan->slug === 'pro')
                                <li class="mb-1"><i class="fas fa-check text-success mr-2"></i>{{ __('Unlimited seats & reports') }}</li>
                                <li class="mb-1"><i class="fas fa-check text-success mr-2"></i>{{ __('Priority support') }}</li>
                            @else
                                <li class="mb-1"><i class="fas fa-check text-success mr-2"></i>{{ __('Great for single campuses') }}</li>
                            @endif
                        </ul>

                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <span class="badge badge-light border mr-2">{{ __('Monthly') }}</span>
                                <span class="text-muted small">{{ __('Billed monthly via Stripe') }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-light border mr-2">{{ __('Yearly') }}</span>
                                <span class="text-muted small">{{ __('Billed annually via Stripe') }}</span>
                            </div>
                        </div>

                        <div class="mt-auto">
                            <form method="POST" action="{{ route('billing.checkout') }}">
                                @csrf
                                <input type="hidden" name="plan_slug" value="{{ $plan->slug }}">
                                <input type="hidden" name="interval" value="monthly">
                                <button type="submit" class="btn btn-primary btn-block">
                                    {{ strtoupper($plan->name) }} – {{ __('Monthly') }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('billing.checkout') }}">
                                @csrf
                                <input type="hidden" name="plan_slug" value="{{ $plan->slug }}">
                                <input type="hidden" name="interval" value="yearly">
                                <button type="submit" class="btn btn-outline-primary btn-block mt-2">
                                    {{ strtoupper($plan->name) }} – {{ __('Yearly') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="col-md-12">
            <div class="card border-info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h3 class="card-title mb-0 text-uppercase">{{ __('ASYLON APEX') }}</h3>
                        <span class="badge badge-info">{{ __('Custom') }}</span>
                    </div>
                    <p class="mb-3 text-muted">
                        {{ __('APEX is tailored for complex environments. Contact us for a custom quote and dedicated rollout support.') }}
                    </p>
                    <a href="{{ url('/contact') }}" class="btn btn-info">
                        {{ __('Contact Us') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
