@php
    /** @var \App\Models\OrgAlertContact|null $alert */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Org> $orgs */
    $acting = auth()->user();
    $isPlatform = $acting?->hasRole('platform_admin');
@endphp

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="type">{{ __('Type') }}</label>
            <select id="type" name="type" class="form-control" required>
                <option value="email" @selected(old('type', $alert->type ?? '') === 'email')>{{ __('Email') }}</option>
                <option value="sms" @selected(old('type', $alert->type ?? '') === 'sms')>{{ __('SMS') }}</option>
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="value">{{ __('Contact') }}</label>
            <input type="text" name="value" id="value" required maxlength="160"
                value="{{ old('value', $alert->value ?? '') }}"
                class="form-control">
            <small class="form-text text-muted">
                {{ __('Email address or phone number to notify.') }}
            </small>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="org_id">{{ __('Organization') }}</label>
    <select id="org_id" name="org_id" class="form-control" @disabled(!$isPlatform)>
        @if ($isPlatform)
            <option value="">{{ __('Select organization') }}</option>
        @endif
        @foreach ($orgs as $org)
            <option value="{{ $org->id }}" @selected((string) old('org_id', $alert->org_id ?? $acting->org_id ?? '') === (string) $org->id)>
                {{ $org->name }}
            </option>
        @endforeach
    </select>
    @if (! $isPlatform)
        <input type="hidden" name="org_id" value="{{ $acting->org_id }}">
        <small class="form-text text-muted">
            {{ __('Contacts will always be linked to your organization.') }}
        </small>
    @endif
</div>

<div class="form-group form-check">
    <input id="is_active" name="is_active" type="checkbox" value="1" class="form-check-input"
        @checked(old('is_active', $alert->is_active ?? true))>
    <label for="is_active" class="form-check-label">{{ __('Active contact') }}</label>
</div>
