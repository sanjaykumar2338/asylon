@extends('layouts.admin', ['headerTitle' => 'Edit Blog Post'])

@section('content')
    <div class="container-fluid">
        <form action="{{ route('admin.blog-posts.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Edit: {{ $post->title }}</h3>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i> Save
                    </button>
                </div>
                <div class="card-body">
                    @include('admin.blog.posts._form')
                </div>
            </div>
        </form>
    </div>
@endsection
