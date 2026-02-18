@php
    $isEdit = isset($page) && $page->exists;
    $languages = config('asylon.languages', []);
    $defaultLocale = config('app.fallback_locale', 'en');
    $currentLocale = old('locale', $formLocale ?? $defaultLocale);
    $localeRoute = $isEdit ? route('admin.pages.edit', $page) : route('admin.pages.create');
@endphp

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trix@2.0.0/dist/trix.css">
    <style>
        trix-editor { min-height: 280px; }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/trix@2.0.0/dist/trix.umd.min.js"></script>
@endpush

<div class="form-group">
    <label for="locale">Content Language</label>
    <select name="locale" id="locale" class="form-control"
            onchange="window.location='{{ $localeRoute }}?lang=' + this.value;">
        @foreach($languages as $code => $label)
            <option value="{{ $code }}" {{ $currentLocale === $code ? 'selected' : '' }}>
                {{ strtoupper($code) }} - {{ $label }}
            </option>
        @endforeach
    </select>
    <small class="text-muted">Switching language reloads the editor for that locale.</small>
</div>

<div class="form-group">
    <label for="title">Title</label>
    <input type="text"
           name="title"
           id="title"
           value="{{ old('title', $page->getTranslation('title', $currentLocale)) }}"
           class="form-control"
           {{ $currentLocale === $defaultLocale ? 'required' : '' }}>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="slug">Slug</label>
        <input type="text" name="slug" id="slug" value="{{ old('slug', $page->slug) }}" class="form-control" placeholder="auto-generated if blank">
    </div>
    <div class="form-group col-md-6">
        <label for="template">Template</label>
        <input type="text" name="template" id="template" value="{{ old('template', $page->template) }}" class="form-control" placeholder="default">
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label for="meta_title">Meta Title</label>
        <input type="text"
               name="meta_title"
               id="meta_title"
               value="{{ old('meta_title', $page->getTranslation('meta_title', $currentLocale)) }}"
               class="form-control">
    </div>
    <div class="form-group col-md-6">
        <label for="meta_description">Meta Description</label>
        <textarea name="meta_description" id="meta_description" rows="2" class="form-control">{{ old('meta_description', $page->getTranslation('meta_description', $currentLocale)) }}</textarea>
    </div>
</div>

<div class="form-group">
    <label for="meta_keywords">Meta Keywords (comma separated)</label>
    <textarea name="meta_keywords" id="meta_keywords" rows="2" class="form-control">{{ old('meta_keywords', $page->getTranslation('meta_keywords', $currentLocale)) }}</textarea>
    <small class="text-muted">Optional keywords for SEO.</small>
</div>

<div class="form-group">
    <label for="content">Content</label>
    <input id="content" type="hidden" name="content" value="{{ old('content', $page->getTranslation('content', $currentLocale)) }}">
    <trix-editor input="content"></trix-editor>
</div>

<div class="form-group form-check">
    <input type="checkbox" name="published" value="1" class="form-check-input" id="published"
           {{ old('published', $page->published ?? true) ? 'checked' : '' }}>
    <label for="published" class="form-check-label">Published</label>
</div>
