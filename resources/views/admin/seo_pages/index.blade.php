@extends('layouts.admin', ['headerTitle' => 'SEO Pages'])

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">SEO Settings</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Slug</th>
                            <th>Meta Title</th>
                            <th>Meta Description</th>
                            <th class="text-right" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($seoPages as $seo)
                            <tr>
                                <td><code>{{ $seo->slug }}</code></td>
                                <td>{{ Str::limit($seo->meta_title, 60) }}</td>
                                <td>{{ Str::limit($seo->meta_description, 80) }}</td>
                                <td class="text-right">
                                    <a href="{{ route('admin.seo-pages.edit', $seo) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $seoPages->links() }}
            </div>
        </div>
    </div>
@endsection
