<x-admin-layout>
    <x-slot name="header">
        {{ __('Pages') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Pages') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Manage site pages and their publish status.') }}</p>
            </div>
            <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> {{ __('New Page') }}
            </a>
        </div>

        <div class="card admin-index-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Slug') }}</th>
                                <th>{{ __('Template') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-right text-nowrap" style="width: 180px;">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pages as $page)
                                <tr>
                                    <td class="font-weight-bold">{{ $page->title }}</td>
                                    <td><code>{{ $page->slug }}</code></td>
                                    <td>{{ $page->template ?? __('default') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $page->published ? 'success' : 'secondary' }}">
                                            {{ $page->published ? __('Published') : __('Draft') }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <div class="d-flex flex-wrap justify-content-end align-items-center" style="gap: 0.5rem;">
                                            <a href="{{ route('pages.show', $page->slug) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('{{ __('Delete this page?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">{{ __('No pages yet.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($pages->hasPages())
                <div class="pt-3 px-3">
                    {{ $pages->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
