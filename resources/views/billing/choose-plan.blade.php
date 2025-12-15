<x-admin-layout>
    <x-slot name="header">
        {{ __('Choose a Plan') }}
    </x-slot>

    @include('admin.partials.flash')

    @php
        $preferred = $org?->preferred_plan;
        $activePlanSlug = ($org?->billing_status === 'active') ? $org?->plan?->slug : null;
        $hasPortal = $org?->stripe_customer_id && $org?->billing_status === 'active';
    @endphp

    @if ($org)
        <div class="alert alert-secondary d-flex justify-content-between align-items-center mb-3">
            <div>
                <strong>{{ __('Organization:') }}</strong> {{ $org->name }}
                <span class="ml-2 badge badge-{{ $org->billing_status === 'active' ? 'success' : 'warning' }}">
                    {{ __('Status:') }} {{ ucfirst($org->billing_status ?? 'pending') }}
                </span>
                @if ($activePlanSlug)
                    <span class="ml-2 badge badge-primary text-uppercase">{{ __('Current Plan:') }} {{ $org->plan?->name ?? strtoupper($activePlanSlug) }}</span>
                @endif
            </div>
            @if ($hasPortal)
                <form method="POST" action="{{ route('billing.portal') }}" class="ml-3">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt mr-1"></i> {{ __('Manage Subscription') }}
                    </button>
                </form>
            @endif
        </div>
    @endif

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
                            @if ($activePlanSlug === $plan->slug)
                                <div class="d-flex flex-column align-items-stretch">
                                    <span class="badge badge-success mb-2">{{ __('Active Subscription') }}</span>
                                    <form method="POST" action="{{ route('billing.portal') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary btn-block">
                                            <i class="fas fa-external-link-alt mr-1"></i> {{ __('Manage Subscription') }}
                                        </button>
                                    </form>
                                </div>
                            @else
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
                            @endif
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
                        <span class="badge badge-info">{{ __('Custom pricing') }}</span>
                    </div>
                    <p class="mb-3 text-muted">
                        {{ __('APEX is built for multi-campus and enterprise deployments. Partner with us for a tailored rollout, governance, and ongoing executive support.') }}
                    </p>
                    <ul class="list-unstyled mb-3">
                        <li class="mb-1"><i class="fas fa-check text-info mr-2"></i>{{ __('Dedicated TAM & priority SLA') }}</li>
                        <li class="mb-1"><i class="fas fa-check text-info mr-2"></i>{{ __('Custom onboarding & training for your teams') }}</li>
                        <li class="mb-1"><i class="fas fa-check text-info mr-2"></i>{{ __('SSO/SAML & advanced security controls') }}</li>
                        <li class="mb-1"><i class="fas fa-check text-info mr-2"></i>{{ __('Unlimited seats, reports, and premium analytics') }}</li>
                        <li class="mb-1"><i class="fas fa-check text-info mr-2"></i>{{ __('Executive reporting & quarterly business reviews') }}</li>
                    </ul>
                    <div class="d-flex flex-wrap align-items-center">
                        <a href="{{ url('/contact') }}" class="btn btn-info mr-2 mb-2">
                            {{ __('Contact Us') }}
                        </a>
                        <button class="btn btn-outline-info mb-2" type="button" data-toggle="collapse" data-target="#apexDetails" aria-expanded="false" aria-controls="apexDetails">
                            {{ __('View details') }}
                        </button>
                    </div>
                    <div class="collapse mt-3" id="apexDetails">
                        <div class="card card-body border-info">
                            <p class="mb-2">{{ __('APEX includes white-glove onboarding, co-authored rollout plans, and direct access to senior support staff.') }}</p>
                            <p class="mb-0 text-muted">{{ __('We will work with your procurement and security teams to meet compliance, SSO, and data governance requirements.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
