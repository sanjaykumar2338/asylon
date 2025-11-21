<x-admin-layout>
    <x-slot name="header">
        {{ __('Trashed Reports') }}
    </x-slot>

    @section('breadcrumb')
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('dashboard.breadcrumb') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reviews.index') }}">{{ __('Review Queue') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Trashed') }}</li>
        </ol>
    @endsection

    <div class="card card-outline card-danger">
        <div class="card-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center">
                <h3 class="card-title mb-0">
                    <i class="fas fa-trash-alt mr-2"></i> {{ __('Trashed Reports') }}
                </h3>
                <a href="{{ route('reviews.index') }}" class="btn btn-outline-secondary btn-sm mt-3 mt-lg-0 ml-lg-3">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('Back to active reports') }}
                </a>
            </div>
            <span class="badge badge-light text-dark px-3 py-2 mt-3 mt-lg-0">
                {{ __('Results') }}: {{ number_format($reports->total()) }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">#</th>
                        @can('view-all')
                            <th scope="col">{{ __('Organization') }}</th>
                        @endcan
                        <th scope="col">{{ __('Category / Subcategory') }}</th>
                        <th scope="col">{{ __('Status') }}</th>
                        <th scope="col">{{ __('Deleted at') }}</th>
                        <th scope="col">{{ __('Submitted at') }}</th>
                        <th scope="col" class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $report)
                        <tr>
                            <td class="font-weight-semibold">
                                <span class="text-monospace">#{{ $report->getKey() }}</span>
                            </td>
                            @can('view-all')
                                <td>{{ $report->org?->name ?? __('Unknown') }}</td>
                            @endcan
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="font-weight-semibold">{{ $report->category }}</span>
                                    <small class="text-muted">
                                        {{ $report->subcategory ?? __('Not provided') }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-secondary text-capitalize">
                                    {{ str_replace('_', ' ', $report->status) }}
                                </span>
                            </td>
                            <td>{{ optional($report->deleted_at)->format('M d, Y H:i') ?? '—' }}</td>
                            <td>{{ optional($report->created_at)->format('M d, Y H:i') ?? '—' }}</td>
                            <td class="text-right">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('reports.show', $report) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-eye mr-1"></i> {{ __('Details') }}
                                    </a>
                                    <form method="POST" action="{{ route('reviews.trash.restore', $report) }}" class="ml-1"
                                        data-swal-confirm
                                        data-swal-title="{{ __('Restore report') }}"
                                        data-swal-message="{{ __('Restore this report to the active queue?') }}"
                                        data-swal-confirm-button="{{ __('Yes, restore') }}"
                                        data-swal-icon="question">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-success">
                                            <i class="fas fa-undo mr-1"></i> {{ __('Restore') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="@can('view-all') 7 @else 6 @endcan" class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p class="mb-0">{{ __('The trash is empty for now.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($reports->hasPages())
            <div class="card-footer">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
