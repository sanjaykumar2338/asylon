@php
    $user = auth()->user();
@endphp

<x-admin-layout>
    <x-slot name="header">
        {{ __('Risk Keywords') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="card card-outline card-primary mb-4">
        <div class="card-header">
            <h3 class="card-title mb-0">
                <i class="fas fa-plus-circle mr-2"></i> {{ __('Add keyword') }}
            </h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.risk-keywords.store') }}" class="form-row">
                @csrf
                <div class="form-group col-md-6">
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
                <div class="form-group col-md-3">
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
                    <div class="form-group col-md-3">
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
                <div class="form-group col-md-12 text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> {{ __('Save keyword') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($user->hasRole('platform_admin') && ($orgs ?? collect())->isNotEmpty())
        <div class="card card-outline card-primary mb-4">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <i class="fas fa-filter mr-2"></i> {{ __('Filter by organization') }}
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.risk-keywords.index') }}" class="form-inline">
                    <label for="org_id" class="sr-only">{{ __('Organization') }}</label>
                    <select name="org_id" id="org_id" class="form-control mr-2">
                        <option value="0">{{ __('All organizations & global') }}</option>
                        @foreach ($orgs as $org)
                            <option value="{{ $org->id }}" @selected((string)$orgFilter === (string)$org->id)>{{ $org->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-search mr-1"></i> {{ __('Apply') }}
                    </button>
                    <a href="{{ route('admin.risk-keywords.index') }}" class="btn btn-link btn-sm ml-2">
                        {{ __('Clear') }}
                    </a>
                </form>
            </div>
        </div>
    @endif

    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-list mr-2"></i> {{ __('Keywords') }}
            </h3>
            <span class="badge badge-info">{{ __('Total') }}: {{ $keywords->total() }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Phrase') }}</th>
                            <th>{{ __('Weight') }}</th>
                            <th>{{ __('Organization') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($keywords as $keyword)
                            <tr>
                                <td>{{ $keyword->phrase }}</td>
                                <td>{{ $keyword->weight }}</td>
                                <td>{{ $keyword->org?->name ?? __('All organizations') }}</td>
                                <td class="text-right">
                                    <form method="POST" action="{{ route('admin.risk-keywords.update', $keyword) }}" class="d-inline-flex align-items-center">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="org_id" value="{{ $keyword->org_id }}">
                                        <input type="text" name="phrase" value="{{ $keyword->phrase }}" class="form-control form-control-sm mr-2" style="width: 200px;">
                                        <input type="number" name="weight" value="{{ $keyword->weight }}" min="1" max="200" class="form-control form-control-sm mr-2" style="width: 100px;">
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
        </div>
        @if ($keywords->hasPages())
            <div class="card-footer">
                {{ $keywords->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</x-admin-layout>
