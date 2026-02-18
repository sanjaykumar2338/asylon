@php
    $allowedRoles = [
        'platform_admin' => 'Platform Admin',
        'executive_admin' => 'Executive Admin',
        'org_admin' => 'Org Admin',
        'security_lead' => 'Security Lead',
        'reviewer' => 'Reviewer',
    ];
@endphp

<div class="form-group">
    <label>{{ __('Name') }}</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $rule->name ?? '') }}" required>
</div>

@if (isset($orgOptions) && $orgOptions->count() > 1 && auth()->user()?->hasRole('platform_admin'))
    <div class="form-group">
        <label>{{ __('Organization') }}</label>
        <select name="org_id" class="form-control">
            <option value="">{{ __('All organizations') }}</option>
            @foreach ($orgOptions as $org)
                <option value="{{ $org->id }}" @selected(old('org_id', $rule->org_id ?? null) == $org->id)>{{ $org->name }}</option>
            @endforeach
        </select>
    </div>
@endif

<div class="form-row">
    <div class="form-group col-md-6">
        <label>{{ __('Minimum risk level') }}</label>
        <select name="min_risk_level" class="form-control">
            @foreach (['low', 'medium', 'high', 'critical'] as $level)
                <option value="{{ $level }}" @selected(old('min_risk_level', $rule->min_risk_level ?? 'high') === $level)>{{ ucfirst($level) }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-6">
        <label>{{ __('Match category (optional)') }}</label>
        <input type="text" name="match_category" class="form-control" value="{{ old('match_category', $rule->match_category ?? '') }}" placeholder="{{ __('Exact category name') }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <div class="form-check mt-4">
            <input type="checkbox" name="match_urgent" id="match_urgent" class="form-check-input" value="1" @checked(old('match_urgent', $rule->match_urgent ?? false))>
            <label class="form-check-label" for="match_urgent">{{ __('Require urgent flag') }}</label>
        </div>
    </div>
    <div class="form-group col-md-6">
        <div class="form-check mt-4">
            <input type="checkbox" name="auto_mark_urgent" id="auto_mark_urgent" class="form-check-input" value="1" @checked(old('auto_mark_urgent', $rule->auto_mark_urgent ?? false))>
            <label class="form-check-label" for="auto_mark_urgent">{{ __('Auto-mark report as urgent') }}</label>
        </div>
    </div>
</div>

<div class="form-group">
    <label>{{ __('Notify roles') }}</label>
    <select name="notify_roles[]" class="form-control" multiple>
        @foreach ($allowedRoles as $key => $label)
            <option value="{{ $key }}"
                @selected(in_array($key, old('notify_roles', $rule->notify_roles ?? ['platform_admin', 'org_admin', 'executive_admin']), true))>
                {{ $label }}
            </option>
        @endforeach
    </select>
    <small class="form-text text-muted">{{ __('Recipients in these roles (within the same org) will get an escalation notification.') }}</small>
</div>

<div class="text-right">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save mr-1"></i> {{ __('Save rule') }}
    </button>
</div>
