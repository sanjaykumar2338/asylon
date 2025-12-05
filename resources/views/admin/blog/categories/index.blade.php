@extends('layouts.admin', ['headerTitle' => 'Blog Categories'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Add Category</h3>
                    </div>
                    <form action="{{ route('admin.blog-categories.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Slug (optional)</label>
                                <input type="text" name="slug" class="form-control" placeholder="auto-generated">
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save mr-1"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Categories</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th class="text-right" style="width: 140px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>{{ $category->name }}</td>
                                        <td><code>{{ $category->slug }}</code></td>
                                        <td class="text-right">
                                            <button class="btn btn-sm btn-outline-primary" type="button" data-toggle="collapse" data-target="#edit-cat-{{ $category->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.blog-categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete category?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <tr class="collapse bg-light" id="edit-cat-{{ $category->id }}">
                                        <td colspan="3">
                                            <form action="{{ route('admin.blog-categories.update', $category) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label>Name</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label>Slug</label>
                                                        <input type="text" name="slug" class="form-control" value="{{ $category->slug }}">
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i> Update</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted p-4">No categories yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
