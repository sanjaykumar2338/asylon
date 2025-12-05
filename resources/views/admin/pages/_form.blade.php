@php
    $isEdit = isset($page) && $page->exists;
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
    <label for="title">Title</label>
    <input type="text" name="title" id="title" value="{{ old('title', $page->title) }}" class="form-control" required>
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
        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $page->meta_title) }}" class="form-control">
    </div>
    <div class="form-group col-md-6">
        <label for="meta_description">Meta Description</label>
        <textarea name="meta_description" id="meta_description" rows="2" class="form-control">{{ old('meta_description', $page->meta_description) }}</textarea>
    </div>
</div>

<div class="form-group">
    <label for="meta_keywords">Meta Keywords (comma separated)</label>
    <textarea name="meta_keywords" id="meta_keywords" rows="2" class="form-control">{{ old('meta_keywords', $page->meta_keywords) }}</textarea>
    <small class="text-muted">Optional keywords for SEO.</small>
</div>

<div class="form-group">
    <label for="content">Content</label>
    <input id="content" type="hidden" name="content" value="{{ old('content', $page->content) }}">
    <trix-editor input="content"></trix-editor>
</div>

<div class="form-group form-check">
    <input type="checkbox" name="published" value="1" class="form-check-input" id="published"
           {{ old('published', $page->published ?? true) ? 'checked' : '' }}>
    <label for="published" class="form-check-label">Published</label>
</div>
