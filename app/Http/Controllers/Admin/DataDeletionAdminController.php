<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataDeletionRequest;
use App\Models\Org;
use App\Models\Report;
use App\Models\User;
use App\Services\DataDeletionProcessor;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DataDeletionAdminController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $status = (string) $request->query('status', '');
        $scope = (string) $request->query('scope', '');
        $orgId = (int) $request->query('org_id', 0);
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');

        $query = DataDeletionRequest::query()
            ->with(['org', 'requester', 'processor'])
            ->orderBy('requested_at', 'desc');

        if (! $user->hasRole('platform_admin')) {
            $query->where('org_id', $user->org_id);
        } elseif ($orgId) {
            $query->where('org_id', $orgId);
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($scope !== '') {
            $query->where('scope', $scope);
        }

        if ($from !== '' && Carbon::hasFormat($from, 'Y-m-d')) {
            $query->whereDate('requested_at', '>=', Carbon::createFromFormat('Y-m-d', $from));
        }

        if ($to !== '' && Carbon::hasFormat($to, 'Y-m-d')) {
            $query->whereDate('requested_at', '<=', Carbon::createFromFormat('Y-m-d', $to));
        }

        $requests = $query->paginate(20)->withQueryString();

        $orgOptions = $user->hasRole('platform_admin')
            ? Org::orderBy('name')->get(['id', 'name'])
            : collect();

        return view('admin.data-requests.index', [
            'requests' => $requests,
            'status' => $status,
            'scope' => $scope,
            'orgId' => $orgId,
            'from' => $from,
            'to' => $to,
            'orgOptions' => $orgOptions,
        ]);
    }

    public function show(DataDeletionRequest $dataRequest): View
    {
        $this->authorizeOrg($dataRequest);

        return view('admin.data-requests.show', [
            'requestItem' => $dataRequest->load(['org', 'requester', 'processor']),
        ]);
    }

    public function updateStatus(Request $request, DataDeletionRequest $dataRequest, DataDeletionProcessor $processor): RedirectResponse
    {
        $this->authorizeOrg($dataRequest);

        $validated = $request->validate([
            'status' => ['required', 'in:new,in_review,completed,rejected'],
            'notes' => ['nullable', 'string'],
        ]);

        $dataRequest->status = $validated['status'];
        $dataRequest->notes = $validated['notes'] ?? $dataRequest->notes;

        if ($validated['status'] === 'completed') {
            $processor->process($dataRequest, $request->user());
        } else {
            $dataRequest->save();
        }

        AuditLogger::log([
            'org_id' => $dataRequest->org_id,
            'user_id' => $request->user()->getKey(),
            'action' => 'data_request_status_change',
            'meta' => [
                'request_id' => $dataRequest->getKey(),
                'status' => $dataRequest->status,
            ],
        ]);

        return back()->with('ok', __('Data deletion request updated.'));
    }

    public function storeFromCase(Request $request, Report $report): RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasRole('platform_admin') && $user->org_id !== $report->org_id) {
            abort(403);
        }

        $dataRequest = DataDeletionRequest::create([
            'org_id' => $report->org_id,
            'user_id' => $user->getKey(),
            'requester_name' => $report->contact_name,
            'requester_email' => $report->contact_email,
            'requester_phone' => $report->contact_phone,
            'scope' => 'cases',
            'reference_type' => 'case',
            'reference_value' => $report->getKey(),
            'status' => 'new',
            'requested_at' => now(),
            'due_at' => now()->addDays(30),
        ]);

        AuditLogger::log([
            'org_id' => $report->org_id,
            'user_id' => $user->getKey(),
            'action' => 'data_request_created_from_case',
            'meta' => [
                'request_id' => $dataRequest->getKey(),
                'case_id' => $report->getKey(),
            ],
        ]);

        return redirect()->route('admin.data_requests.show', $dataRequest)->with('ok', __('Data deletion request created.'));
    }

    protected function authorizeOrg(DataDeletionRequest $request): void
    {
        $user = auth()->user();

        if (! $user) {
            abort(403);
        }

        if ($user->hasRole('platform_admin')) {
            return;
        }

        if ($user->org_id !== $request->org_id) {
            abort(403);
        }
    }
}
