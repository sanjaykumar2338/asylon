@php
    /** @var \App\Models\Org|null $org */
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
