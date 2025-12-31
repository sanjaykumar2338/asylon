<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends AdminController
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): View
    {
        $search = (string) $request->query('q', '');
        $authUser = $request->user();
        $isGlobal = $authUser?->isPlatformAdmin() || $authUser?->isSuperAdmin();
        $orgId = $isGlobal ? (int) $request->query('org_id', 0) : 0;

        $query = User::query()
            ->with('org')
            ->orderByDesc('created_at');

        $this->scopeByRole($query);

        if ($orgId > 0 && $isGlobal) {
            $query->where('org_id', $orgId);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $search,
            'orgOptions' => $this->orgOptions(),
            'orgId' => $orgId,
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('admin.users.create', [
            'orgs' => $this->orgOptions(),
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $this->prepareUserData($request->validated(), $request);

        $user = User::create([
            ...$data,
            'password' => Str::password(),
        ]);

        try {
            Password::sendResetLink(['email' => $user->email]);
        } catch (\Throwable $exception) {
            report($exception);
        }

        return redirect()->route('admin.users.index')
            ->with('status', 'User created. A password reset link has been queued.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $this->authorizeUserAccess($user);

        return view('admin.users.edit', [
            'user' => $user,
            'orgs' => $this->orgOptions(),
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorizeUserAccess($user);

        $data = $this->prepareUserData($request->validated(), $request, $user);

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('status', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorizeUserAccess($user);

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('status', 'User removed.');
    }

    /**
     * Ensure the authenticated user may access the target user.
     */
    protected function authorizeUserAccess(User $user): void
    {
        $authUser = auth()->user();

        if (! $authUser) {
            abort(403);
        }

        if ($authUser->isSuperAdmin()) {
            return;
        }

        if ($authUser->isPlatformAdmin()) {
            if ($user->isSuperAdmin()) {
                abort(403);
            }

            return;
        }

        if ($authUser->org_id !== $user->org_id) {
            abort(403);
        }

        if ($user->isPlatformAdmin() || $user->isSuperAdmin()) {
            abort(403);
        }
    }

    /**
     * Prepare validated user data respecting the acting user's permissions.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function prepareUserData(array $data, Request $request, ?User $existing = null): array
    {
        $authUser = $request->user();
        $role = $data['role'] ?? $existing?->role ?? null;

        $data['active'] = $request->boolean('active', $existing?->active ?? true);

        if ($authUser && ! $authUser->isSuperAdmin()) {
            if ($role === 'super_admin') {
                abort(403, 'Only super administrators can assign the super admin role.');
            }

            if (! $authUser->isPlatformAdmin()) {
                $data['org_id'] = $authUser->org_id;

                if (in_array($role, ['platform_admin', 'super_admin'], true)) {
                    abort(403, 'Org administrators cannot assign global roles.');
                }
            } else {
                if ($role === 'platform_admin') {
                    $data['org_id'] = null;
                }
            }
        }

        if (in_array($role, ['platform_admin', 'super_admin'], true)) {
            $data['org_id'] = null;
        }

        if (in_array($role, ['org_admin', 'executive_admin', 'security_lead', 'reviewer', 'org_user'], true) && empty($data['org_id'])) {
            throw ValidationException::withMessages([
                'org_id' => 'An organization is required for this role.',
            ]);
        }

        return $data;
    }
}
