<x-admin-layout>
    <x-slot name="header">
        {{ __('Edit Organization') }}
    </x-slot>

    
    <div class="row">
        <div class="col-lg-8">
            @include('admin.partials.flash')

            @if ($org->org_code)
                <div class="card card-outline card-secondary mb-4">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('Public report link') }}</h5>
                        <p class="card-text text-muted">
                            {{ __('Share this link with team members to route reports directly to this organization.') }}
                        </p>
                        <div class="d-flex flex-column flex-md-row align-items-md-center">
                            <input type="text" class="form-control mb-2 mb-md-0" readonly value="{{ $org->reportUrl(true) }}">
                            <button type="button" class="btn btn-primary ml-md-2" onclick="navigator.clipboard.writeText('{{ $org->reportUrl(true) }}')">
                                <i class="fas fa-copy mr-1"></i> {{ __('Copy link') }}
                            </button>
                        </div>
                        {{-- {!! QrCode::size(160)->generate($org->reportUrl(true)) !!} --}}
                    </div>
                </div>
            @endif

            <div class="card card-outline card-primary">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.orgs.update', $org) }}">
                        @csrf
                        @method('PUT')

                        @include('admin.orgs.form', ['org' => $org])

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('admin.orgs.index') }}" class="btn btn-outline-secondary">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary ml-2">
                                <i class="fas fa-save mr-1"></i> {{ __('Update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
