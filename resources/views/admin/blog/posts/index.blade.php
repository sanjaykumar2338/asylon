<x-admin-layout>
    <x-slot name="header">
        {{ __('Blog Posts') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Blog Posts') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Manage blog posts, categories, and publish status.') }}</p>
            </div>
            <a href="{{ route('admin.blog-posts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> {{ __('New Post') }}
            </a>
        </div>

        <div class="card admin-index-card mb-3">
            <div class="card-body">
                <form method="GET" class="admin-filter-bar mb-0">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4 col-lg-3">
                            <label class="sr-only" for="search-posts">{{ __('Search title') }}</label>
                            <input type="text" id="search-posts" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('Search title') }}">
                        </div>
                        <div class="col-md-3 col-lg-2">
                            <label class="sr-only" for="status">{{ __('Status') }}</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">{{ __('All statuses') }}</option>
                                <option value="draft" @selected(request('status')==='draft')>{{ __('Draft') }}</option>
                                <option value="published" @selected(request('status')==='published')>{{ __('Published') }}</option>
                            </select>
                        </div>
                            <div class="col-md-3 col-lg-3">
                                <label class="sr-only" for="category">{{ __('Category') }}</label>
                                <select name="category" id="category" class="form-control">
                                    <option value="">{{ __('All categories') }}</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" @selected(request('category')==$cat->id)>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        <div class="col-md-2 col-lg-2 text-md-end">
                            <button class="btn btn-outline-primary mr-1" type="submit">
                                <i class="fas fa-filter mr-1"></i> {{ __('Filter') }}
                            </button>
                            @if(request()->hasAny(['q','status','category']) && (request('q') || request('status') || request('category')))
                                <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-light btn-sm">
                                    {{ __('Clear') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card admin-index-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Published') }}</th>
                                <th class="text-right text-nowrap" style="width: 160px;">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($posts as $post)
                                <tr>
                                    <td class="font-weight-bold">{{ $post->title }}</td>
                                    <td>{{ $post->category?->name ?? '—' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $post->status === 'published' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($post->status) }}
                                        </span>
                                    </td>
                                    <td>{{ optional($post->published_at)->format('Y-m-d') ?? '—' }}</td>
                                    <td class="text-right">
                                        <div class="d-flex flex-wrap justify-content-end align-items-center" style="gap: 0.5rem;">
                                            <a href="{{ route('admin.blog-posts.edit', $post) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('admin.blog-posts.destroy', $post) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Delete post?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">{{ __('No posts found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($posts->hasPages())
                <div class="pt-3 px-3">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
