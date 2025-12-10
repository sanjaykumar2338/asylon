<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Org;
use App\Models\Plan;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', '');
        $planSlug = (string) $request->query('plan', '');
        $planId = (string) $request->query('plan_id', '');
        $search = (string) $request->query('search', '');
        $type = (string) $request->query('type', '');

        $query = Org::query()->with('plan')->orderByDesc('created_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('short_name', 'like', "%{$search}%")
                    ->orWhere('contact_email', 'like', "%{$search}%");
            });
        }

        if ($status !== '') {
            $query->where('billing_status', $status);
        }

        if ($planSlug !== '') {
            $planId = Plan::where('slug', $planSlug)->value('id');
            if ($planId) {
                $query->where('plan_id', $planId);
            }
        } elseif ($planId !== '') {
            $query->where('plan_id', $planId);
        }

        if ($type !== '') {
            $query->where('org_type', $type);
        }

        $orgs = $query->paginate(25)->withQueryString();
        $plans = Plan::orderBy('name')->get(['id', 'name']);

        return view('platform.organizations.index', [
            'orgs' => $orgs,
            'status' => $status,
            'planId' => $planId,
            'planSlug' => $planSlug,
            'search' => $search,
            'type' => $type,
            'plans' => $plans,
        ]);
    }

    public function show(Org $org): View
    {
        $org->load('plan');
        $plans = Plan::orderBy('name')->get(['id', 'name']);

        return view('platform.organizations.show', [
            'org' => $org,
            'plans' => $plans,
        ]);
    }

    public function updatePlan(Request $request, Org $org): RedirectResponse
    {
        $data = $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
        ]);

        $org->update([
            'plan_id' => $data['plan_id'],
        ]);

        AuditLogger::log([
            'org_id' => $org->id,
            'user_id' => $request->user()->getKey(),
            'action' => 'org_plan_updated',
            'meta' => [
                'plan_id' => $data['plan_id'],
            ],
        ]);

        return back()->with('ok', __('Plan updated.'));
    }

    public function updateStatus(Request $request, Org $org): RedirectResponse
    {
        $data = $request->validate([
            'billing_status' => ['required', 'in:active,trialing,suspended'],
        ]);

        $org->update([
            'billing_status' => $data['billing_status'],
        ]);

        AuditLogger::log([
            'org_id' => $org->id,
            'user_id' => $request->user()->getKey(),
            'action' => 'org_status_updated',
            'meta' => [
                'billing_status' => $data['billing_status'],
            ],
        ]);

        return back()->with('ok', __('Billing status updated.'));
    }
}
