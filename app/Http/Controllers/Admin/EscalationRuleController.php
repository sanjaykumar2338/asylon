<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EscalationRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EscalationRuleController extends AdminController
{
    public function index(): View
    {
        $user = auth()->user();
        $query = EscalationRule::query()->with('org');

        if ($user && ! $user->hasRole('platform_admin')) {
            $query->where('org_id', $user->org_id);
        }

        $rules = $query->orderBy('name')->get();

        return view('admin.escalation-rules.index', [
            'rules' => $rules,
            'orgOptions' => $this->orgOptions(),
        ]);
    }

    public function create(): View
    {
        return view('admin.escalation-rules.create', [
            'orgOptions' => $this->orgOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        EscalationRule::create($data);

        return redirect()->route('admin.escalation-rules.index')->with('ok', __('Escalation rule created.'));
    }

    public function edit(EscalationRule $escalation_rule): View
    {
        $this->authorizeOrgRule($escalation_rule);

        return view('admin.escalation-rules.edit', [
            'rule' => $escalation_rule,
            'orgOptions' => $this->orgOptions(),
        ]);
    }

    public function update(Request $request, EscalationRule $escalation_rule): RedirectResponse
    {
        $this->authorizeOrgRule($escalation_rule);

        $data = $this->validatedData($request);
        $escalation_rule->update($data);

        return redirect()->route('admin.escalation-rules.index')->with('ok', __('Escalation rule updated.'));
    }

    public function destroy(EscalationRule $escalation_rule): RedirectResponse
    {
        $this->authorizeOrgRule($escalation_rule);
        $escalation_rule->delete();

        return redirect()->route('admin.escalation-rules.index')->with('ok', __('Escalation rule deleted.'));
    }

    /**
     * Validate request data.
     *
     * @return array<string, mixed>
     */
    protected function validatedData(Request $request): array
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'org_id' => ['nullable', 'integer', 'exists:orgs,id'],
            'min_risk_level' => ['required', 'in:low,medium,high,critical'],
            'match_urgent' => ['sometimes', 'boolean'],
            'match_category' => ['nullable', 'string', 'max:255'],
            'auto_mark_urgent' => ['sometimes', 'boolean'],
            'notify_roles' => ['nullable', 'array'],
            'notify_roles.*' => ['string'],
        ]);

        if (! $user->hasRole('platform_admin')) {
            $data['org_id'] = $user->org_id;
        }

        $data['match_urgent'] = $request->boolean('match_urgent');
        $data['auto_mark_urgent'] = $request->boolean('auto_mark_urgent');
        $data['notify_roles'] = array_values(array_unique($data['notify_roles'] ?? []));

        return $data;
    }

    protected function authorizeOrgRule(EscalationRule $rule): void
    {
        $user = auth()->user();
        if ($user && ! $user->hasRole('platform_admin') && $rule->org_id !== $user->org_id) {
            abort(403);
        }
    }
}
