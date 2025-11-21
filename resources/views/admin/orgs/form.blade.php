@php
    /** @var \App\Models\Org|null $org */
    /** @var \Illuminate\Support\Collection<int, \App\Models\User> $eligibleUsers */
    $eligibleUsers = $eligibleUsers ?? collect();
    $supportedLocales = config('app.supported_locales', ['en']);
    $localeNames = config('app.locale_names', []);
@endphp

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">{{ __('Name') }}</label>
            <input type="text" name="name" id="name" required maxlength="120"
                value="{{ old('name', $org->name ?? '') }}"
                class="form-control">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="slug">
                {{ __('Slug') }}
                <small class="text-muted">({{ __('URL-friendly identifier, e.g. asylon-high') }})</small>
            </label>
            <input type="text" name="slug" id="slug" required maxlength="140"
                value="{{ old('slug', $org->slug ?? '') }}"
                class="form-control">
            <small class="form-text text-muted">
                {{ __('Used in URLs and integrations. Must be unique.') }}
            </small>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="status">{{ __('Status') }}</label>
    <select id="status" name="status" class="form-control" required>
        <option value="active" @selected(old('status', $org->status ?? '') === 'active')>{{ __('Active') }}</option>
        <option value="inactive" @selected(old('status', $org->status ?? '') === 'inactive')>{{ __('Inactive') }}</option>
    </select>
</div>

<div class="form-group">
    <label for="default_locale">{{ __('general.default_language') }}</label>
    <select id="default_locale" name="default_locale" class="form-control" required>
        @foreach ($supportedLocales as $locale)
            <option value="{{ $locale }}" @selected(old('default_locale', $org->default_locale ?? 'en') === $locale)>
                {{ $localeNames[$locale] ?? strtoupper($locale) }}
            </option>
        @endforeach
    </select>
    <small class="form-text text-muted">
        {{ __('general.default_language_hint') }}
    </small>
</div>

<div class="form-group">
    <label class="d-block">{{ __('Report types') }}</label>
    <input type="hidden" name="enable_student_reports" value="0">
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="enable_student_reports" name="enable_student_reports" value="1"
            @checked(old('enable_student_reports', $org->enable_student_reports ?? true))>
        <label class="custom-control-label" for="enable_student_reports">
            {{ __('Enable student reporting') }}
        </label>
    </div>
    <input type="hidden" name="enable_commendations" value="0">
    <div class="custom-control custom-checkbox mt-2">
        <input type="checkbox" class="custom-control-input" id="enable_commendations" name="enable_commendations" value="1"
            @checked(old('enable_commendations', $org->enable_commendations ?? false))>
        <label class="custom-control-label" for="enable_commendations">
            {{ __('Enable commendations') }}
        </label>
    </div>
    <input type="hidden" name="enable_hr_reports" value="0">
    <div class="custom-control custom-checkbox mt-2">
        <input type="checkbox" class="custom-control-input" id="enable_hr_reports" name="enable_hr_reports" value="1"
            @checked(old('enable_hr_reports', $org->enable_hr_reports ?? false))>
        <label class="custom-control-label" for="enable_hr_reports">
            {{ __('Enable HR / anonymous employee reports') }}
        </label>
    </div>
    <small class="form-text text-muted">
        {{ __('Safety reports are always enabled. Disable other types here if they do not apply to this organization.') }}
    </small>
</div>

<div class="form-group">
    <label for="on_call_user_id">{{ __('On-call reviewer') }}</label>
    @if ($eligibleUsers->isEmpty())
        <select id="on_call_user_id" class="form-control" disabled>
            <option>{{ __('No eligible reviewers available') }}</option>
        </select>
        <small class="form-text text-muted">
            {{ __('Invite active reviewers or security leads to assign an on-call contact.') }}
        </small>
    @else
        <select id="on_call_user_id" name="on_call_user_id" class="form-control">
            <option value="">{{ __('None selected') }}</option>
            @foreach ($eligibleUsers as $user)
                <option value="{{ $user->id }}"
                    @selected((string) old('on_call_user_id', $org->on_call_user_id ?? '') === (string) $user->id)>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
        <small class="form-text text-muted">
            {{ __('This reviewer receives direct notifications for urgent reports.') }}
        </small>
    @endif
    @error('on_call_user_id')
        <span class="text-danger small d-block mt-1">{{ $message }}</span>
    @enderror
</div>
