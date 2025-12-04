@extends('layouts.admin', ['headerTitle' => 'Edit Page'])

@section('content')
    <div class="container-fluid">
        <form action="{{ route('admin.pages.update', $page) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Edit: {{ $page->title }}</h3>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('pages.show', $page->slug) }}" target="_blank" class="btn btn-outline-secondary btn-sm mr-2">
                            <i class="fas fa-external-link-alt mr-1"></i> View
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-save mr-1"></i> Save
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @include('admin.pages._form', ['page' => $page])
                </div>
            </div>
        </form>
    </div>
@endsection
