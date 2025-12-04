@extends('layouts.admin', ['headerTitle' => 'Pages'])

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Pages</h3>
                <a href="{{ route('admin.pages.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> New Page
                </a>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Template</th>
                            <th>Status</th>
                            <th class="text-right" style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pages as $page)
                            <tr>
                                <td>{{ $page->title }}</td>
                                <td><code>{{ $page->slug }}</code></td>
                                <td>{{ $page->template ?? 'default' }}</td>
                                <td>
                                    <span class="badge badge-{{ $page->published ? 'success' : 'secondary' }}">
                                        {{ $page->published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('pages.show', $page->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this page?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted p-4">No pages yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $pages->links() }}
            </div>
        </div>
    </div>
@endsection
