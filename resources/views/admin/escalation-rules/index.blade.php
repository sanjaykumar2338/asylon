<x-admin-layout>
    <x-slot name="header">
        {{ __('Escalation Rules') }}
    </x-slot>

    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">{{ __('Rules') }}</h3>
            <a href="{{ route('admin.escalation-rules.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> {{ __('Add rule') }}
            </a>
        </div>
        <div class="card-body p-0">
            @if ($rules->isEmpty())
                <p class="text-muted p-3 mb-0">{{ __('No escalation rules yet.') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
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
                            @foreach ($rules as $rule)
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
