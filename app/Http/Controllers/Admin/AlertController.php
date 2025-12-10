<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreAlertRequest;
use App\Http\Requests\Admin\UpdateAlertRequest;
use App\Models\OrgAlertContact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AlertController extends AdminController
{
    /**
     * Display a listing of the alert contacts.
     */
    public function index(Request $request): View
    {
        $type = (string) $request->query('type', '');
        $search = (string) $request->query('q', '');
        $orgId = $request->user()?->hasRole('platform_admin') ? (int) $request->query('org_id', 0) : 0;

        $query = OrgAlertContact::query()
            ->with('org')
            ->orderByDesc('created_at');

        $this->scopeByRole($query);

        if ($orgId > 0 && $request->user()?->hasRole('platform_admin')) {
            $query->where('org_id', $orgId);
        }

        if ($type !== '') {
            $query->where('type', $type);
        }

        if ($search !== '') {
            $query->where('value', 'like', "%{$search}%");
        }

        $alerts = $query->paginate(15)->withQueryString();

        return view('admin.alerts.index', [
            'alerts' => $alerts,
            'type' => $type,
            'search' => $search,
            'orgId' => $orgId,
            'orgOptions' => $this->orgOptions(),
        ]);
    }

    /**
     * Show the form for creating a new alert contact.
     */
    public function create(): View
    {
        return view('admin.alerts.create', [
            'orgs' => $this->orgOptions(),
            'departments' => $this->alertDepartments(),
        ]);
    }

    /**
     * Store a newly created alert contact in storage.
     */
    public function store(StoreAlertRequest $request): RedirectResponse
    {
        $data = $this->prepareAlertData($request->validated(), $request);

        OrgAlertContact::create($data);

        return redirect()->route('admin.alerts.index')
            ->with('status', 'Alert contact added.');
    }

    /**
     * Show the form for editing the specified alert contact.
     */
    public function edit(OrgAlertContact $alert): View
    {
        $this->authorizeAlertAccess($alert);

        return view('admin.alerts.edit', [
            'alert' => $alert,
            'orgs' => $this->orgOptions(),
            'departments' => $this->alertDepartments(),
        ]);
    }

    /**
     * Update the specified alert contact in storage.
     */
    public function update(UpdateAlertRequest $request, OrgAlertContact $alert): RedirectResponse
    {
        $this->authorizeAlertAccess($alert);

        $alert->update($this->prepareAlertData($request->validated(), $request));

        return redirect()->route('admin.alerts.index')
            ->with('status', 'Alert contact updated.');
    }

    /**
     * Remove the specified alert contact from storage.
     */
    public function destroy(OrgAlertContact $alert): RedirectResponse
    {
        $this->authorizeAlertAccess($alert);

        $alert->delete();

        return redirect()->route('admin.alerts.index')
            ->with('status', 'Alert contact removed.');
    }

    /**
     * Ensure the authenticated user may manage the contact.
     */
    protected function authorizeAlertAccess(OrgAlertContact $alert): void
    {
        $user = auth()->user();

        if (! $user) {
            abort(403);
        }

        if ($user->hasRole('platform_admin')) {
            return;
        }

        if ($user->org_id !== $alert->org_id) {
            abort(403);
        }
    }

    /**
     * Prepare alert data respecting role constraints.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function prepareAlertData(array $data, Request $request): array
    {
        $user = $request->user();

        $data['is_active'] = $request->boolean('is_active', $data['is_active'] ?? true);
        $data['department'] = $data['department'] ?? null;

        if ($user && ! $user->hasRole('platform_admin')) {
            $data['org_id'] = $user->org_id;
        }

        if (empty($data['org_id'])) {
            throw ValidationException::withMessages([
                'org_id' => 'Select a valid organization for this contact.',
            ]);
        }

        return $data;
    }

    /**
     * Department options for alert contacts.
     *
     * @return array<string, string>
     */
    protected function alertDepartments(): array
    {
        return config('asylon.alerts.departments', []);
    }
}
