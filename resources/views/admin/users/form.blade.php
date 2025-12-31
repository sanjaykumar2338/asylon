@php
    /** @var \App\Models\User|null $user */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Org> $orgs */
    $editing = isset($user);
    $acting = auth()->user();
    $isPlatform = $acting?->isPlatformAdmin();
    $isSuper = $acting?->isSuperAdmin();
    $roleOptions = ['platform_admin' => 'Platform Admin', 'org_admin' => 'Org Admin', 'security_lead' => 'Security Lead', 'reviewer' => 'Reviewer'];

    if ($isSuper) {
        $roleOptions = ['super_admin' => 'Super Admin', ...$roleOptions];
    }
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
            <label for="phone">{{ __('Phone (E.164 preferred)') }}</label>
            <input type="text" name="phone" id="phone" maxlength="30"
                value="{{ old('phone', $user->phone ?? '') }}"
                class="form-control">
            <small class="form-text text-muted">
                {{ __('Required for SMS two-factor codes and alerts.') }}
            </small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="role">{{ __('Role') }}</label>
            <select id="role" name="role" class="form-control" required>
                @foreach ($roleOptions as $value => $label)
                    @continue(!$isPlatform && !$isSuper && $value === 'platform_admin')
                    @continue(!$isSuper && $value === 'super_admin')
                    <option value="{{ $value }}" @selected(old('role', $user->role ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group" data-org-wrapper>
            <label for="org_id">{{ __('Organization') }}</label>
            <select id="org_id" name="org_id" class="form-control" @disabled(!$isPlatform && !$isSuper)>
                <option value="">-- None --</option>
                @foreach ($orgs as $org)
                    <option value="{{ $org->id }}" @selected((string) old('org_id', $user->org_id ?? '') === (string) $org->id)>
                        {{ $org->name }}
                    </option>
                @endforeach
            </select>
            @if (! $isPlatform && ! $isSuper)
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

<div class="alert alert-info mt-3">
    <strong>{{ __('SMS Safety Alerts') }}</strong><br>
    {{ __("By adding this staff member, you confirm that their phone number comes from internal HR records and that they are part of your organization's safety/incident response process. They may receive safety, security, and incident-related SMS alerts from Asylon as part of their role. Message frequency may vary. Msg & data rates may apply. They can reply STOP at any time to opt out.") }}
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const role = document.getElementById('role');
            const orgWrapper = document.querySelector('[data-org-wrapper]');
            const orgSelect = document.getElementById('org_id');
            const globalRoles = ['platform_admin', 'super_admin'];

            const toggleOrg = () => {
                if (!role || !orgSelect || !orgWrapper) {
                    return;
                }

                const shouldHideOrg = globalRoles.includes(role.value);
                if (shouldHideOrg) {
                    orgSelect.value = '';
                    orgSelect.setAttribute('disabled', 'disabled');
                    orgWrapper.classList.add('d-none');
                } else {
                    orgSelect.removeAttribute('disabled');
                    orgWrapper.classList.remove('d-none');
                }
            };

            toggleOrg();
            role?.addEventListener('change', toggleOrg);
        });
    </script>
@endpush
