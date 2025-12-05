@extends('layouts.admin', ['headerTitle' => 'New Blog Post'])

@section('content')
    <div class="container-fluid">
        <form action="{{ route('admin.blog-posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Create Post</h3>
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
