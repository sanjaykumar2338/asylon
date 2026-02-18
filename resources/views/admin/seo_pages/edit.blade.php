@extends('layouts.admin', ['headerTitle' => 'Edit SEO'])

@section('content')
    <div class="container-fluid">
        <form action="{{ route('admin.seo-pages.update', $seoPage) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">SEO: {{ $seoPage->slug }}</h3>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i> Save
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $seoPage->meta_title) }}">
                    </div>
                    <div class="form-group">
                        <label>Meta Description</label>
                        <textarea name="meta_description" rows="3" class="form-control">{{ old('meta_description', $seoPage->meta_description) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Meta Keywords</label>
                        <textarea name="meta_keywords" rows="2" class="form-control">{{ old('meta_keywords', $seoPage->meta_keywords) }}</textarea>
                        <small class="text-muted">Comma-separated</small>
                    </div>
                    <div class="form-group">
                        <label>H1 Override</label>
                        <input type="text" name="h1_override" class="form-control" value="{{ old('h1_override', $seoPage->h1_override) }}">
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
