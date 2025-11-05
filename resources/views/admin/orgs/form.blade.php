@php
    /** @var \App\Models\Org|null $org */
    /** @var \Illuminate\Support\Collection<int, \App\Models\User> $eligibleUsers */
    $eligibleUsers = $eligibleUsers ?? collect();
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
            <label for="slug">{{ __('Slug') }}</label>
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
