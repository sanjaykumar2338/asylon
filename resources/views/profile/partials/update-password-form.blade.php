<div class="card card-outline card-primary mb-4">
    <div class="card-header">
        <h3 class="card-title mb-0">{{ __('Update Password') }}</h3>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-4">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>

        @if (session('status') === 'password-updated')
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i> {{ __('Password updated successfully.') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <div class="form-group">
                <label for="update_password_current_password">{{ __('Current Password') }}</label>
                <input id="update_password_current_password" name="current_password" type="password"
                    class="form-control @if ($errors->updatePassword->has('current_password')) is-invalid @endif"
                    autocomplete="current-password">
                @foreach ($errors->updatePassword->get('current_password') as $message)
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @endforeach
            </div>

            <div class="form-group">
                <label for="update_password_password">{{ __('New Password') }}</label>
                <input id="update_password_password" name="password" type="password"
                    class="form-control @if ($errors->updatePassword->has('password')) is-invalid @endif"
                    autocomplete="new-password">
                @foreach ($errors->updatePassword->get('password') as $message)
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @endforeach
            </div>

            <div class="form-group">
                <label for="update_password_password_confirmation">{{ __('Confirm Password') }}</label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                    class="form-control @if ($errors->updatePassword->has('password_confirmation')) is-invalid @endif"
                    autocomplete="new-password">
                @foreach ($errors->updatePassword->get('password_confirmation') as $message)
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @endforeach
            </div>

            <div class="text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> {{ __('Save Password') }}
                </button>
            </div>
        </form>
    </div>
</div>

