@php
    /** @var \App\Models\User|null $user */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Org> $orgs */
    $editing = isset($user);
    $acting = auth()->user();
    $isPlatform = $acting?->hasRole('platform_admin');
@endphp

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">{{ __('Name') }}</label>
            <input type="text" name="name" id="name" required maxlength="120"
                value="{{ old('name', $user->name ?? '') }}"
                class="form-control">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="email">{{ __('Email') }}</label>
            <input type="email" name="email" id="email" required maxlength="255"
                value="{{ old('email', $user->email ?? '') }}"
                class="form-control">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="role">{{ __('Role') }}</label>
            <select id="role" name="role" class="form-control" required>
                @foreach (['platform_admin' => 'Platform Admin', 'org_admin' => 'Org Admin', 'security_lead' => 'Security Lead', 'reviewer' => 'Reviewer'] as $value => $label)
                    @continue(!$isPlatform && $value === 'platform_admin')
                    <option value="{{ $value }}" @selected(old('role', $user->role ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="org_id">{{ __('Organization') }}</label>
            <select id="org_id" name="org_id" class="form-control" @disabled(!$isPlatform)>
                <option value="">-- None --</option>
                @foreach ($orgs as $org)
                    <option value="{{ $org->id }}" @selected((string) old('org_id', $user->org_id ?? '') === (string) $org->id)>
                        {{ $org->name }}
                    </option>
                @endforeach
            </select>
            @if (! $isPlatform)
                <input type="hidden" name="org_id" value="{{ $acting->org_id }}">
                <small class="form-text text-muted">
                    {{ __('Org admins can only assign members to their own organization.') }}
                </small>
            @endif
        </div>
    </div>
</div>

<div class="form-group form-check">
    <input id="active" name="active" type="checkbox" value="1" class="form-check-input"
        @checked(old('active', $user->active ?? true))>
    <label for="active" class="form-check-label">{{ __('Active account') }}</label>
</div>

@unless($editing)
    <small class="text-muted d-block">
        {{ __('New users receive a password reset link so they can set their password securely.') }}
    </small>
@endunless

