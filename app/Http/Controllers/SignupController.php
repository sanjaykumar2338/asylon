<?php

namespace App\Http\Controllers;

use App\Models\Org;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SignupController extends Controller
{
    public function showForm(): View
    {
        $plans = Plan::query()->where('is_active', true)->orderBy('id')->get(['id', 'name', 'slug', 'trial_days']);

        return view('signup.get-started', [
            'plans' => $plans,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'org_name' => ['required', 'string', 'max:255'],
            'org_type' => ['required', Rule::in(['school', 'church', 'organization', 'other'])],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'plan_slug' => ['nullable', Rule::exists('plans', 'slug')],
        ]);

        $preferredPlanSlug = $validated['plan_slug'] ?? null;
        $preferredPlanName = $preferredPlanSlug
            ? Plan::where('slug', $preferredPlanSlug)->value('name')
            : null;

        $org = Org::create([
            'name' => $validated['org_name'],
            'slug' => \Illuminate\Support\Str::slug($validated['org_name']).'-'.substr(uniqid(), -4),
            'status' => 'active',
            'enable_commendations' => true,
            'enable_hr_reports' => true,
            'enable_student_reports' => true,
            'plan_id' => null,
            'preferred_plan' => $preferredPlanSlug,
            'billing_status' => 'pending',
            'trial_ends_at' => null,
            'is_self_service' => true,
        ]);

        $user = User::create([
            'org_id' => $org->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'executive_admin',
            'active' => true,
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        return redirect()
            ->route('billing.choose_plan')
            ->with('ok', $preferredPlanName
                ? __('You selected the :plan plan. Please complete checkout to activate your subscription.', ['plan' => $preferredPlanName])
                : __('Please choose a plan to activate your subscription.'));
    }

    public function welcome(): View
    {
        return view('signup.welcome');
    }
}
