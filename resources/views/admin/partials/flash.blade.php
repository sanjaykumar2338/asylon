@php
    $status = session('status');
    $statusMessages = [
        'profile-updated' => __('Your profile has been updated.'),
        'password-updated' => __('Password updated successfully.'),
        'verification-link-sent' => __('A new verification link has been sent to your email address.'),
    ];
    $statusText = $statusMessages[$status] ?? $status;
@endphp

@if ($status)
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i> {{ $statusText }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" data-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h5 class="mb-2"><i class="icon fas fa-exclamation-triangle"></i> {{ __('Something went wrong!') }}</h5>
        <ul class="mb-0 pl-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" data-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    </div>
@endif
