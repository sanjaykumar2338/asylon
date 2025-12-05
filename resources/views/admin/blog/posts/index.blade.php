@extends('layouts.admin', ['headerTitle' => 'Blog Posts'])

@section('content')
    <div class="container-fluid">
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="form-inline">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control mr-2" placeholder="Search title">
                    <select name="status" class="form-control mr-2">
                        <option value="">All statuses</option>
                        <option value="draft" @selected(request('status')==='draft')>Draft</option>
                        <option value="published" @selected(request('status')==='published')>Published</option>
                    </select>
                    <select name="category" class="form-control mr-2">
                        <option value="">All categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(request('category')==$cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-filter mr-1"></i> Filter</button>
                    <a href="{{ route('admin.blog-posts.create') }}" class="btn btn-primary btn-sm ml-auto">
                        <i class="fas fa-plus mr-1"></i> New Post
                    </a>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Published</th>
                            <th class="text-right" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                            <tr>
                                <td>{{ $post->title }}</td>
                                <td>{{ $post->category?->name ?? '—' }}</td>
                                <td>
                                    <span class="badge badge-{{ $post->status === 'published' ? 'success' : 'secondary' }}">{{ ucfirst($post->status) }}</span>
                                </td>
                                <td>{{ optional($post->published_at)->format('Y-m-d') ?? '—' }}</td>
                                <td class="text-right">
                                    <a href="{{ route('admin.blog-posts.edit', $post) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.blog-posts.destroy', $post) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete post?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted p-4">No posts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
@endsection
