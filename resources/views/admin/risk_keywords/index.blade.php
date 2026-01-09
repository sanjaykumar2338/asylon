@php
    $user = auth()->user();
@endphp

<x-admin-layout>
    <x-slot name="header">
        {{ __('Risk Keywords') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Risk Keywords') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Create weighted phrases to flag risky content across orgs.') }}</p>
            </div>
            <span class="badge badge-info badge-pill px-3 py-2">{{ __('Total') }}: {{ $keywords->total() }}</span>
        </div>

        <div class="card admin-index-card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h2 class="h6 mb-0 text-uppercase text-muted">{{ __('Add keyword') }}</h2>
                </div>
                <form method="POST" action="{{ route('admin.risk-keywords.store') }}" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-6">
                        <label for="phrase">{{ __('Phrase or slang') }}</label>
                        <input id="phrase" name="phrase" type="text" required maxlength="200"
                            class="form-control" value="{{ old('phrase') }}">
                        <small class="form-text text-muted">
                            {{ __('Add slang, street terms, or sensitive keywords to flag during risk analysis.') }}
                        </small>
                        @error('phrase')
                            <span class="text-danger small d-block mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="weight">{{ __('Weight') }}</label>
                        <input id="weight" name="weight" type="number" min="1" max="200" required
                            class="form-control" value="{{ old('weight', 20) }}">
                        <small class="form-text text-muted">
                            {{ __('Higher weights increase the risk score when matched.') }}
                        </small>
                        @error('weight')
                            <span class="text-danger small d-block mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    @if ($user->hasRole('platform_admin'))
                        <div class="col-md-3">
                            <label for="org_id">{{ __('Organization scope') }}</label>
                            <select id="org_id" name="org_id" class="form-control">
                                <option value="">{{ __('All organizations (global)') }}</option>
                                @foreach ($orgs as $org)
                                    <option value="{{ $org->id }}" @selected(old('org_id') == $org->id)>{{ $org->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                {{ __('Leave blank for global keywords, or target a specific org.') }}
                            </small>
                        </div>
                    @else
                        <input type="hidden" name="org_id" value="{{ $userOrgId }}">
                    @endif
                    <div class="col-12 text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> {{ __('Save keyword') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if ($user->hasRole('platform_admin') && ($orgs ?? collect())->isNotEmpty())
            <div class="card admin-index-card mb-3">
                <div class="card-body">
                    <h2 class="h6 mb-3 text-uppercase text-muted">{{ __('Filter by organization') }}</h2>
                    <form method="GET" action="{{ route('admin.risk-keywords.index') }}" class="admin-filter-bar mb-0">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-6 col-lg-4">
                                <label for="org_id" class="sr-only">{{ __('Organization') }}</label>
                                <select name="org_id" id="org_id" class="form-control">
                                    <option value="0">{{ __('All organizations & global') }}</option>
                                    @foreach ($orgs as $org)
                                        <option value="{{ $org->id }}" @selected((string)$orgFilter === (string)$org->id)>{{ $org->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-4 text-md-end">
                                <button type="submit" class="btn btn-outline-primary me-1">
                                    <i class="fas fa-filter mr-1"></i> {{ __('Apply') }}
                                </button>
                                <a href="{{ route('admin.risk-keywords.index') }}" class="btn btn-light">
                                    {{ __('Clear') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <div class="card admin-index-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h6 mb-0 text-uppercase text-muted">{{ __('Keywords') }}</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('Phrase') }}</th>
                                <th class="text-nowrap">{{ __('Weight') }}</th>
                                <th>{{ __('Organization') }}</th>
                                <th class="text-right text-nowrap">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($keywords as $keyword)
                                <tr>
                                    <td>{{ $keyword->phrase }}</td>
                                    <td>{{ $keyword->weight }}</td>
                                    <td>{{ $keyword->org?->name ?? __('All organizations') }}</td>
                                    <td class="text-right">
                                        <div class="d-flex flex-wrap justify-content-end align-items-center" style="gap: 0.5rem;">
                                            <form method="POST" action="{{ route('admin.risk-keywords.update', $keyword) }}" class="d-flex flex-wrap align-items-center" style="gap: 0.5rem;">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="org_id" value="{{ $keyword->org_id }}">
                                                <input type="text" name="phrase" value="{{ $keyword->phrase }}" class="form-control form-control-sm" style="min-width: 180px;">
                                                <input type="number" name="weight" value="{{ $keyword->weight }}" min="1" max="200" class="form-control form-control-sm" style="width: 90px;">
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.risk-keywords.destroy', $keyword) }}" class="d-inline"
                                                onsubmit="return confirm('{{ __('Delete this keyword?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        {{ __('No keywords added yet.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($keywords->hasPages())
                    <div class="pt-3">
                        {{ $keywords->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
