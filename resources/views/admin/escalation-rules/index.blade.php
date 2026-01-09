<x-admin-layout>
    <x-slot name="header">
        {{ __('Escalation Rules') }}
    </x-slot>

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 admin-page-header">
            <div>
                <h1 class="h4 mb-1">{{ __('Escalation Rules') }}</h1>
                <p class="text-muted mb-0 small">{{ __('Configure automatic escalation based on risk and urgency.') }}</p>
            </div>
            <a href="{{ route('admin.escalation-rules.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> {{ __('Add rule') }}
            </a>
        </div>

        <div class="card admin-index-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Org') }}</th>
                                <th>{{ __('Min risk') }}</th>
                                <th>{{ __('Urgent required') }}</th>
                                <th>{{ __('Category match') }}</th>
                                <th>{{ __('Auto mark urgent') }}</th>
                                <th class="text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rules as $rule)
                                <tr>
                                    <td>{{ $rule->name }}</td>
                                    <td>{{ $rule->org?->name ?? __('All orgs') }}</td>
                                    <td class="text-capitalize">{{ $rule->min_risk_level }}</td>
                                    <td>{{ $rule->match_urgent ? __('Yes') : __('No') }}</td>
                                    <td>{{ $rule->match_category ?: '—' }}</td>
                                    <td>{{ $rule->auto_mark_urgent ? __('Yes') : __('No') }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.escalation-rules.edit', $rule) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-edit mr-1"></i> {{ __('Edit') }}
                                        </a>
                                        <form action="{{ route('admin.escalation-rules.destroy', $rule) }}" method="POST" class="d-inline-block"
                                            onsubmit="return confirm('{{ __('Delete this rule?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash mr-1"></i> {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        {{ __('No escalation rules yet.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
