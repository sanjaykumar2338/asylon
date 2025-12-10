<x-admin-layout>
    <x-slot name="header">
        {{ __('Organization Settings') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title mb-0">
                <i class="fas fa-cog mr-2"></i> {{ __('Organization Profile') }}
            </h3>
        </div>
        <div class="card-body">
            @if(auth()->user()?->hasRole('platform_admin') && ($orgOptions ?? collect())->isNotEmpty())
                <form method="GET" action="{{ route('settings.organization.edit') }}" class="form-inline mb-4">
                    <label for="org_id" class="mr-2 font-weight-bold">{{ __('Select organization') }}</label>
                    <select id="org_id" name="org_id" class="form-control mr-2">
                        @foreach ($orgOptions as $option)
                            <option value="{{ $option->id }}" @selected((string) request('org_id') === (string) $option->id || $org->id === $option->id)>{{ $option->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        {{ __('Load') }}
                    </button>
                </form>
            @endif

            <form method="POST" action="{{ route('settings.organization.update') }}" enctype="multipart/form-data">
                @csrf
                @if(auth()->user()?->hasRole('platform_admin'))
                    <input type="hidden" name="org_id" value="{{ $org->id }}">
                @endif
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="name">{{ __('Organization Name') }}</label>
                        <input type="text" id="name" name="name" class="form-control" required maxlength="255" value="{{ old('name', $org->name) }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="short_name">{{ __('Short Name') }}</label>
                        <input type="text" id="short_name" name="short_name" class="form-control" maxlength="100" value="{{ old('short_name', $org->short_name) }}">
                        <small class="text-muted">{{ __('Used in headers or compact layouts.') }}</small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="contact_email">{{ __('Contact Email') }}</label>
                        <input type="email" id="contact_email" name="contact_email" class="form-control" maxlength="255" value="{{ old('contact_email', $org->contact_email) }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="contact_phone">{{ __('Contact Phone') }}</label>
                        <input type="text" id="contact_phone" name="contact_phone" class="form-control" maxlength="50" value="{{ old('contact_phone', $org->contact_phone) }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="primary_color">{{ __('Primary Color') }}</label>
                        <input type="text" id="primary_color" name="primary_color" class="form-control" maxlength="20" value="{{ old('primary_color', $org->primary_color) }}" placeholder="#1f2937">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="logo">{{ __('Logo') }}</label>
                        <input type="file" id="logo" name="logo" class="form-control-file">
                        @if ($org->logo_path)
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$org->logo_path) }}" alt="{{ __('Logo') }}" style="max-height: 80px;">
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group form-check">
                    <input type="hidden" name="enable_ultra_private_mode" value="0">
                    <input type="checkbox" id="enable_ultra_private_mode" name="enable_ultra_private_mode" value="1" class="form-check-input" @checked(old('enable_ultra_private_mode', $org->enable_ultra_private_mode))>
                    <label class="form-check-label" for="enable_ultra_private_mode">{{ __('Enable ultra private mode') }}</label>
                </div>

                <div class="card card-outline card-light mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Plan & Usage') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                    <div class="col-md-3">
                        <div class="text-muted small text-uppercase">{{ __('Plan') }}</div>
                        <div>{{ $org->plan->name ?? __('Starter') }}</div>
                    </div>
                            <div class="col-md-3">
                                <div class="text-muted small text-uppercase">{{ __('Billing Status') }}</div>
                                <div class="text-capitalize">{{ $org->billing_status ?? 'active' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted small text-uppercase">{{ __('Trial Ends') }}</div>
                                <div>{{ $org->trial_ends_at ? $org->trial_ends_at->format('M d, Y') : __('N/A') }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted small text-uppercase">{{ __('Reports This Month') }}</div>
                                <div>{{ $org->reports_this_month_label ?? $org->reports_this_month }}</div>
                            </div>
                            <div class="col-md-3 mt-3">
                                <div class="text-muted small text-uppercase">{{ __('Total Reports') }}</div>
                                <div>{{ number_format($org->total_reports ?? 0) }}</div>
                            </div>
                            <div class="col-md-3 mt-3">
                                <div class="text-muted small text-uppercase">{{ __('Seats Used') }}</div>
                                <div>{{ $org->seats_used_label ?? $org->seats_used }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> {{ __('Save changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
