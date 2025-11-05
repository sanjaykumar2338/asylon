<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreOrgRequest;
use App\Http\Requests\Admin\UpdateOrgRequest;
use App\Models\Org;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class OrgController extends AdminController
{
    /**
     * Display a listing of the organizations.
     */
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', '');
        $search = (string) $request->query('q', '');

        $query = Org::query()->orderBy('name');
        $this->scopeByRole($query);

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $orgs = $query->paginate(15)->withQueryString();

        return view('admin.orgs.index', [
            'orgs' => $orgs,
            'status' => $status,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new organization.
     */
    public function create(): View
    {
        $this->ensurePlatformAdmin();

        return view('admin.orgs.create', [
            'eligibleUsers' => collect(),
        ]);
    }

    /**
     * Store a newly created organization in storage.
     */
    public function store(StoreOrgRequest $request): RedirectResponse
    {
        $this->ensurePlatformAdmin();

        Org::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('admin.orgs.index')
            ->with('status', 'Organization created successfully.');
    }

    /**
     * Show the form for editing the specified organization.
     */
    public function edit(Org $org): View
    {
        $this->authorizeOrgAccess($org);

        return view('admin.orgs.edit', [
            'org' => $org,
            'eligibleUsers' => $this->eligibleOnCallUsers($org),
        ]);
    }

    /**
     * Update the specified organization in storage.
     */
    public function update(UpdateOrgRequest $request, Org $org): RedirectResponse
    {
        $this->authorizeOrgAccess($org);

        $org->update($request->validated());

        return redirect()->route('admin.orgs.index')
            ->with('status', 'Organization updated successfully.');
    }

    /**
     * Remove the specified organization from storage.
     */
    public function destroy(Org $org): RedirectResponse
    {
        $this->ensurePlatformAdmin();

        $org->delete();

        return redirect()->route('admin.orgs.index')
            ->with('status', 'Organization removed.');
    }

    /**
     * Ensure the current user is a platform admin.
     */
    protected function ensurePlatformAdmin(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->hasRole('platform_admin')) {
            abort(403);
        }
    }

    /**
     * Ensure the current user can access the given organization.
     */
    protected function authorizeOrgAccess(Org $org): void
    {
        $user = auth()->user();

        if (! $user) {
            abort(403);
        }

        if ($user->hasRole('platform_admin')) {
            return;
        }

        if ($user->org_id !== $org->id) {
            abort(403);
        }
    }

    /**
     * Get active reviewers for the given org that can be assigned as on-call.
     *
     * @return Collection<int, \App\Models\User>
     */
    protected function eligibleOnCallUsers(Org $org): Collection
    {
        return $org->users()
            ->whereIn('role', ['reviewer', 'security_lead'])
            ->where('active', true)
            ->orderBy('name')
            ->get();
    }
}
