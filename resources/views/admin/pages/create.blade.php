@extends('layouts.admin', ['headerTitle' => 'New Page'])

@section('content')
    <div class="container-fluid">
        <form action="{{ route('admin.pages.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Create Page</h3>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i> Save
                    </button>
                </div>
                <div class="card-body">
                    @include('admin.pages._form', ['page' => $page])
                </div>
            </div>
        </form>
    </div>
@endsection
