<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Models\Org;

class OrgSettingsController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        if (! $user || ! $user->hasRole(['platform_admin', 'executive_admin', 'org_admin'])) {
            abort(403);
        }

        $org = $this->resolveOrg($request);

        if (! $org) {
            abort(404, 'Organization not found for this user.');
        }

        return view('settings.organization', [
            'org' => $org,
            'orgOptions' => $user->hasRole('platform_admin') ? $this->orgOptions() : collect(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user || ! $user->hasRole(['platform_admin', 'executive_admin', 'org_admin'])) {
            abort(403);
        }

        $org = $this->resolveOrg($request);

        if (! $org) {
            abort(404, 'Organization not found for this user.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:100'],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'enable_ultra_private_mode' => ['nullable', 'boolean'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $logoPath = $org->logo_path;

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'org-logos/'.$org->id.'-'.Str::random(6).'.'.$file->getClientOriginalExtension();
            $logoPath = $file->storePublicly($filename, 'public');
        }

        $org->update([
            'name' => $data['name'],
            'short_name' => $data['short_name'] ?? null,
            'contact_email' => $data['contact_email'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? null,
            'primary_color' => $data['primary_color'] ?? null,
            'logo_path' => $logoPath,
            'enable_ultra_private_mode' => $request->boolean('enable_ultra_private_mode'),
        ]);

        return redirect()
            ->route('settings.organization.edit', $user->hasRole('platform_admin') ? ['org_id' => $org->id] : [])
            ->with('ok', __('Organization settings updated.'));
    }

    protected function resolveOrg(Request $request)
    {
        $user = $request->user();

        if ($user?->hasRole('platform_admin')) {
            $orgId = $request->input('org_id', $request->query('org_id'));

            if ($orgId) {
                return \App\Models\Org::find($orgId);
            }

            return \App\Models\Org::first();
        }

        return $user?->org;
    }

    protected function orgOptions(): Collection
    {
        return Org::query()->orderBy('name')->get(['id', 'name']);
    }
}
