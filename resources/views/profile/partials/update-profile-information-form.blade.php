<div class="card card-outline card-primary mb-4">
    <div class="card-header">
        <h3 class="card-title mb-0">{{ __('Profile Information') }}</h3>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-4">
            {{ __("Update your account's profile information and email address.") }}
        </p>

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        @if (session('status') === 'profile-updated')
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i> {{ __('Your profile has been updated.') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('status') === 'verification-link-sent')
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle mr-2"></i> {{ __('A new verification link has been sent to your email address.') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('patch')

            <div class="form-group">
                <label>{{ __('Profile photo') }}</label>
                <div class="d-flex align-items-center">
                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="rounded-circle border"
                        style="width: 70px; height: 70px; object-fit: cover;">
                    <div class="ml-3 flex-grow-1">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input @error('avatar') is-invalid @enderror"
                                id="avatar" name="avatar" accept="image/*">
                            <label class="custom-file-label" for="avatar">{{ __('Choose image') }}</label>
                        </div>
                        <small class="form-text text-muted">
                            {{ __('Accepted formats: JPG, PNG, GIF. Max size 2 MB.') }}
                        </small>
                        @error('avatar')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="name">{{ __('Name') }}</label>
                <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $user->email) }}" required autocomplete="username">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="alert alert-warning mt-3" role="alert">
                        <div class="d-flex align-items-center justify-content-between">
                            <span>{{ __('Your email address is unverified.') }}</span>
                            <button type="submit" form="send-verification" class="btn btn-warning btn-sm">
                                <i class="fas fa-paper-plane mr-1"></i> {{ __('Resend verification email') }}
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <div class="text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> {{ __('Save Changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
