@php
    $isEdit = isset($post) && $post->exists;
@endphp

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trix@2.0.0/dist/trix.css">
    <style>trix-editor{min-height:320px;}</style>
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/trix@2.0.0/dist/trix.umd.min.js"></script>
@endpush

<div class="form-row">
    <div class="form-group col-md-8">
        <label>Title</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $post->title) }}" required>
    </div>
    <div class="form-group col-md-4">
        <label>Slug</label>
        <input type="text" name="slug" class="form-control" value="{{ old('slug', $post->slug) }}" placeholder="auto-generated">
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label>Category</label>
        <select name="category_id" class="form-control">
            <option value="">None</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $post->category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-4">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="draft" @selected(old('status', $post->status) === 'draft')>Draft</option>
            <option value="published" @selected(old('status', $post->status) === 'published')>Published</option>
        </select>
    </div>
    <div class="form-group col-md-4">
        <label>Published At</label>
        <input type="datetime-local" name="published_at" class="form-control"
               value="{{ old('published_at', optional($post->published_at)->format('Y-m-d\TH:i')) }}">
    </div>
</div>

<div class="form-group">
    <label>Excerpt</label>
    <textarea name="excerpt" rows="2" class="form-control">{{ old('excerpt', $post->excerpt) }}</textarea>
    <small class="text-muted">Short summary for list/SEO.</small>
</div>

<div class="form-group">
    <label>Content</label>
    <input id="content" type="hidden" name="content" value="{{ old('content', $post->content) }}">
    <trix-editor input="content"></trix-editor>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label>Featured Image URL</label>
        <input type="text" name="featured_image" class="form-control" value="{{ old('featured_image', $post->featured_image) }}" placeholder="https://... or leave blank to upload below">
        @if($post->featuredImageUrl())
            <small class="text-muted d-block mt-1">Current: <a href="{{ $post->featuredImageUrl() }}" target="_blank">{{ $post->featuredImageUrl() }}</a></small>
        @endif
    </div>
    <div class="form-group col-md-6">
        <label>Featured Image Upload</label>
        <input type="file" name="featured_image_upload" class="form-control-file">
        <small class="text-muted">Optional upload (stored in public storage)</small>
    </div>
</div>
<div class="form-group">
    <label>Featured Image Alt</label>
    <input type="text" name="featured_image_alt" class="form-control" value="{{ old('featured_image_alt', $post->featured_image_alt) }}">
</div>

<div class="form-group">
    <label>Author (optional)</label>
    <input type="text" name="author_name" class="form-control" value="{{ old('author_name', $post->author_name) }}">
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label>Meta Title</label>
        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $post->meta_title) }}">
    </div>
    <div class="form-group col-md-6">
        <label>Meta Description</label>
        <textarea name="meta_description" rows="2" class="form-control">{{ old('meta_description', $post->meta_description) }}</textarea>
    </div>
</div>

<div class="form-group">
    <label>Meta Keywords</label>
    <textarea name="meta_keywords" rows="2" class="form-control">{{ old('meta_keywords', $post->meta_keywords) }}</textarea>
    <small class="text-muted">Comma-separated</small>
</div>
