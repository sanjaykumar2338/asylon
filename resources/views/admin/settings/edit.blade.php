<x-admin-layout>
    <x-slot name="header">
        {{ __('Platform Settings') }}
    </x-slot>

    <div class="container-fluid">
        @include('admin.partials.flash')

        <div class="card admin-index-card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf

                    <div class="custom-control custom-switch mb-4">
                        <input type="hidden" name="sms_enabled" value="0">
                        <input type="checkbox" class="custom-control-input" id="sms_enabled" name="sms_enabled" value="1" {{ old('sms_enabled', $sms_enabled ? '1' : '0') == '1' ? 'checked' : '' }}>
                        <label class="custom-control-label font-weight-bold" for="sms_enabled">
                            {{ __('Enable SMS alerts') }}
                        </label>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Admin Two-Factor Authentication') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="custom-control custom-switch mb-3">
                                <input type="hidden" name="admin_2fa_enabled" value="0">
                                <input type="checkbox" class="custom-control-input" id="admin_2fa_enabled" name="admin_2fa_enabled" value="1" {{ old('admin_2fa_enabled', $admin_2fa_enabled ? '1' : '0') == '1' ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="admin_2fa_enabled">
                                    {{ __('Require 2FA for admin roles') }}
                                </label>
                            </div>

                            <div class="custom-control custom-switch mb-3">
                                <input type="hidden" name="admin_2fa_email" value="0">
                                <input type="checkbox" class="custom-control-input" id="admin_2fa_email" name="admin_2fa_email" value="1" {{ old('admin_2fa_email', $admin_2fa_email ? '1' : '0') == '1' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="admin_2fa_email">
                                    {{ __('Send codes via email') }}
                                </label>
                            </div>

                            <div class="custom-control custom-switch">
                                <input type="hidden" name="admin_2fa_sms" value="0">
                                <input type="checkbox" class="custom-control-input" id="admin_2fa_sms" name="admin_2fa_sms" value="1" {{ old('admin_2fa_sms', $admin_2fa_sms ? '1' : '0') == '1' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="admin_2fa_sms">
                                    {{ __('Send codes via SMS') }}
                                </label>
                                <small class="form-text text-muted">
                                    {{ __('Requires a valid phone number on the user profile and Telnyx SMS settings.') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="telnyx_api_key">{{ __('Telnyx API Key') }}</label>
                        <input type="password"
                            id="telnyx_api_key"
                            name="telnyx_api_key"
                            class="form-control @error('telnyx_api_key') is-invalid @enderror"
                            value="{{ old('telnyx_api_key', $telnyx_api_key) }}"
                            placeholder="sk_live_...">
                        <small class="form-text text-muted">
                            {{ __('Stored encrypted. Paste or edit the live key from your Telnyx dashboard.') }}
                        </small>
                        @error('telnyx_api_key')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="telnyx_from_number">{{ __('From number (E.164)') }}</label>
                        <input type="text"
                            id="telnyx_from_number"
                            name="telnyx_from_number"
                            class="form-control @error('telnyx_from_number') is-invalid @enderror"
                            value="{{ old('telnyx_from_number', $telnyx_from_number) }}"
                            placeholder="+12145550123">
                        <small class="form-text text-muted">
                            {{ __('US long code used when alpha sender is unavailable.') }}
                        </small>
                        @error('telnyx_from_number')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="telnyx_alpha_sender">{{ __('Alphanumeric Sender ID') }}</label>
                        <input type="text"
                            id="telnyx_alpha_sender"
                            name="telnyx_alpha_sender"
                            class="form-control @error('telnyx_alpha_sender') is-invalid @enderror"
                            value="{{ old('telnyx_alpha_sender', $telnyx_alpha_sender) }}"
                            placeholder="ASYLON">
                        <small class="form-text text-muted">
                            {{ __('Max 11 characters. Use for countries that support registered alpha senders.') }}
                        </small>
                        @error('telnyx_alpha_sender')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="custom-control custom-switch mb-4">
                        <input type="hidden" name="telnyx_enable_alpha" value="0">
                        <input type="checkbox" class="custom-control-input" id="telnyx_enable_alpha" name="telnyx_enable_alpha" value="1" {{ old('telnyx_enable_alpha', $telnyx_enable_alpha ? '1' : '0') == '1' ? 'checked' : '' }}>
                        <label class="custom-control-label font-weight-bold" for="telnyx_enable_alpha">
                            {{ __('Use alpha sender for non-US numbers') }}
                        </label>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> {{ __('Save settings') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card admin-index-card mt-4">
        <div class="card-body">
            <div class="card mb-0">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('SMTP test email') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        {{ __('This sends a single message using the SMTP settings configured in .env. Check the recipient inbox or spam folder and consult the logs if the test fails.') }}
                    </p>
                    <form method="POST" action="{{ route('admin.settings.test_email') }}">
                        @csrf
                        <div class="form-row align-items-center g-0">
                            <div class="col">
                                <input type="email"
                                    name="recipient_email"
                                    id="recipient_email"
                                    class="form-control @error('recipient_email') is-invalid @enderror"
                                    value="{{ old('recipient_email') }}"
                                    placeholder="platform-admin@example.com"
                                    required>
                                @error('recipient_email')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-auto ml-2">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-paper-plane mr-1"></i> {{ __('Send test email') }}
                                </button>
                            </div>
                        </div>
                    </form>
                    <small class="form-text text-muted mt-2">
                        {{ __('Errors are logged with the current admin ID without exposing secrets in the UI.') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
