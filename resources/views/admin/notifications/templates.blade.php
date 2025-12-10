<x-admin-layout>
    <x-slot name="header">
        {{ __('Notification Templates') }}
    </x-slot>

    <div class="row">
        <div class="col-lg-12">
            @include('admin.partials.flash')
            @if ($usingGlobalDefaults ?? false)
                <div class="alert alert-info">
                    {{ __('You are currently using the default system templates. Create a template to override the default for your organization.') }}
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('SMS Templates') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        {{ __('Auto-compliance:') }} {{ $complianceLine }}
                    </p>

                    @foreach(($templates['sms'] ?? []) as $type => $template)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">{{ $template['label'] }}</h6>
                                <span class="badge {{ $template['source'] === 'org' ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $template['source'] === 'org' ? __('Custom Template Active') : __('Using Global Default') }}
                                </span>
                            </div>
                            <form method="POST" action="{{ route('admin.notifications.templates.update') }}">
                                @csrf
                                <input type="hidden" name="channel" value="sms">
                                <input type="hidden" name="type" value="{{ $type }}">

                                @php
                                    $bodyValue = old('channel') === 'sms' && old('type') === $type
                                        ? old('body', $template['body'] ?? '')
                                        : ($template['body'] ?? '');
                                @endphp
                                <div class="form-group">
                                    <label for="sms_body_{{ $type }}">{{ __('Body') }}</label>
                                    <textarea
                                        id="sms_body_{{ $type }}"
                                        name="body"
                                        rows="5"
                                        class="form-control @error('body') is-invalid @enderror">{{ $bodyValue }}</textarea>
                                    <small class="form-text text-muted">
                                        {{ __('Placeholders:') }}
                                        {{ collect($template['placeholders'])->map(fn ($p) => '{' . $p . '}')->implode(', ') }}
                                    </small>
                                    @error('body')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-save mr-1"></i> {{ __('Save Template') }}
                                    </button>
                                    <button type="submit" name="restore" value="1" class="btn btn-outline-secondary btn-sm">
                                        {{ __('Restore Default Template') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Email Templates') }}</h5>
                </div>
                <div class="card-body">
                    @foreach(($templates['email'] ?? []) as $type => $template)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">{{ $template['label'] }}</h6>
                                <span class="badge {{ $template['source'] === 'org' ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $template['source'] === 'org' ? __('Custom Template Active') : __('Using Global Default') }}
                                </span>
                            </div>
                            <form method="POST" action="{{ route('admin.notifications.templates.update') }}">
                                @csrf
                                <input type="hidden" name="channel" value="email">
                                <input type="hidden" name="type" value="{{ $type }}">

                                @php
                                    $subjectValue = old('channel') === 'email' && old('type') === $type
                                        ? old('subject', $template['subject'] ?? '')
                                        : ($template['subject'] ?? '');
                                    $bodyValue = old('channel') === 'email' && old('type') === $type
                                        ? old('body', $template['body'] ?? '')
                                        : ($template['body'] ?? '');
                                @endphp
                                <div class="form-group">
                                    <label for="email_subject_{{ $type }}">{{ __('Subject') }}</label>
                                    <input
                                        id="email_subject_{{ $type }}"
                                        type="text"
                                        name="subject"
                                        value="{{ $subjectValue }}"
                                        class="form-control @error('subject') is-invalid @enderror">
                                    @error('subject')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="email_body_{{ $type }}">{{ __('Body') }}</label>
                                    <textarea
                                        id="email_body_{{ $type }}"
                                        name="body"
                                        rows="8"
                                        class="form-control @error('body') is-invalid @enderror">{{ $bodyValue }}</textarea>
                                    <small class="form-text text-muted">
                                        {{ __('Placeholders:') }}
                                        {{ collect($template['placeholders'])->map(fn ($p) => '{' . $p . '}')->implode(', ') }}
                                    </small>
                                    @error('body')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-save mr-1"></i> {{ __('Save Template') }}
                                    </button>
                                    <button type="submit" name="restore" value="1" class="btn btn-outline-secondary btn-sm">
                                        {{ __('Restore Default Template') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
