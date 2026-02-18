<x-admin-layout>
    <x-slot name="header">
        {{ __('Edit Blog Post') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Edit Post') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Update the blog post title, content, category, and publish status.') }}</p>
            </div>
        </div>

        <form action="{{ route('admin.blog-posts.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card admin-index-card">
                <div class="card-body">
                    @include('admin.blog.posts._form')
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-outline-secondary px-3 py-2">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary ml-2 px-3 py-2">
                        <i class="fas fa-save mr-1"></i> {{ __('Save') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-admin-layout>
