<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Org;
use App\Models\RiskKeyword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RiskKeywordController extends Controller
{
    /**
        * Display and manage risk keywords.
        */
    public function index(Request $request): View
    {
        $user = $request->user();
        $orgFilter = $user->hasRole('platform_admin') ? (int) $request->query('org_id', 0) : 0;

        $query = RiskKeyword::query()->orderBy('phrase');

        if (! $user->hasRole('platform_admin')) {
            $query->where('org_id', $user->org_id);
        } elseif ($orgFilter > 0) {
            $query->where('org_id', $orgFilter);
        }

        $keywords = $query->with('org')->paginate(20)->withQueryString();

        $orgs = collect();
        if ($user->hasRole('platform_admin')) {
            $orgs = Org::query()->orderBy('name')->get(['id', 'name']);
        }

        return view('admin.risk_keywords.index', [
            'keywords' => $keywords,
            'orgs' => $orgs,
            'userOrgId' => $user->org_id,
            'orgFilter' => $orgFilter,
        ]);
    }

    /**
     * Store a new keyword.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $orgId = $this->resolveOrgId($request);

        if (! $user->hasRole('platform_admin') && $user->org_id !== $orgId) {
            abort(403);
        }

        $validated = $this->validateKeyword($request, null, $orgId);

        RiskKeyword::create($validated + ['org_id' => $orgId]);

        return back()->with('status', __('Keyword added.'));
    }

    /**
     * Update an existing keyword.
     */
    public function update(Request $request, RiskKeyword $riskKeyword): RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasRole('platform_admin') && $user->org_id !== $riskKeyword->org_id) {
            abort(403);
        }

        $orgId = $this->resolveOrgId($request, $riskKeyword->org_id);
        $validated = $this->validateKeyword($request, $riskKeyword->id, $orgId);

        $riskKeyword->update($validated + ['org_id' => $orgId]);

        return back()->with('status', __('Keyword updated.'));
    }

    /**
     * Remove a keyword.
     */
    public function destroy(Request $request, RiskKeyword $riskKeyword): RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasRole('platform_admin') && $user->org_id !== $riskKeyword->org_id) {
            abort(403);
        }

        $riskKeyword->delete();

        return back()->with('status', __('Keyword removed.'));
    }

    /**
     * Validate input for storing/updating.
     */
    protected function validateKeyword(Request $request, ?int $ignoreId, ?int $orgId): array
    {
        $unique = Rule::unique('risk_keywords', 'phrase');
        $unique = $orgId
            ? $unique->where('org_id', $orgId)
            : $unique->whereNull('org_id');

        if ($ignoreId) {
            $unique = $unique->ignore($ignoreId);
        }

        return $request->validate([
            'phrase' => ['required', 'string', 'max:200', $unique],
            'weight' => ['required', 'integer', 'min:1', 'max:200'],
        ]);
    }

    /**
     * Determine org context for the keyword.
     */
    protected function resolveOrgId(Request $request, ?int $fallback = null): ?int
    {
        $user = $request->user();

        if ($user->hasRole('platform_admin')) {
            $orgId = $request->input('org_id');
            return $orgId !== null && $orgId !== '' ? (int) $orgId : null;
        }

        return $user->org_id ?? $fallback;
    }
}
