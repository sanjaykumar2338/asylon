<div class="card admin-index-card mb-4">
    <div class="card-header">
        <h3 class="card-title mb-0">{{ __('Delete Account') }}</h3>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-4">
            {{ __('Once your account is deleted, all of its resources and data will be permanently removed. Download any data you wish to keep before proceeding.') }}
        </p>
        <button type="button" class="btn btn-danger px-3 py-2" data-toggle="modal" data-target="#confirmUserDeletionModal">
            <i class="fas fa-user-slash mr-1"></i> {{ __('Delete Account') }}
        </button>
    </div>
</div>

<div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" role="dialog" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="modal-header">
                    <h5 class="modal-title" id="confirmUserDeletionModalLabel">{{ __('Delete Account') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        {{ __('This action is permanent. Please confirm your password to continue.') }}
                    </p>
                    <div class="form-group mb-0">
                        <label for="delete_account_password">{{ __('Password') }}</label>
                        <input type="password" class="form-control @if ($errors->userDeletion->has('password')) is-invalid @endif"
                            id="delete_account_password" name="password" placeholder="{{ __('Password') }}" required>
                        @foreach ($errors->userDeletion->get('password') as $message)
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-user-slash mr-1"></i> {{ __('Delete Account') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(function () {
            @if ($errors->userDeletion->isNotEmpty())
                $('#confirmUserDeletionModal').modal('show');
            @endif
        });
    </script>
@endpush
