@extends('layouts.admin', ['headerTitle' => 'New Menu'])

@section('content')
    <div class="container-fluid">
        <form action="{{ route('admin.menus.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Create Menu</h3>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i> Save
                    </button>
                </div>
                <div class="card-body">
                    @include('admin.menus.partials.form', ['menu' => $menu])
                </div>
            </div>
        </form>
    </div>
@endsection
