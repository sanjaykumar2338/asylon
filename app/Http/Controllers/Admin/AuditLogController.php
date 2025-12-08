<?php


namespace App\Http\Controllers\Admin;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AuditLogController extends AdminController
{
    /**
     * Display paginated audit logs scoped to the admin's organization.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');
        $userId = (string) $request->query('user_id', '');
        $action = (string) $request->query('action', '');
        $caseId = (string) $request->query('case_id', '');

        $query = AuditLog::query()->with(['user', 'org', 'case']);
        $this->scopeByRole($query, 'org_id');

        if ($from !== '' && Carbon::hasFormat($from, 'Y-m-d')) {
            $query->whereDate('created_at', '>=', Carbon::createFromFormat('Y-m-d', $from));
        }

        if ($to !== '' && Carbon::hasFormat($to, 'Y-m-d')) {
            $query->whereDate('created_at', '<=', Carbon::createFromFormat('Y-m-d', $to));
        }

        if ($userId !== '') {
            $query->where('user_id', $userId);
        }

        if ($action !== '') {
            $query->where('action', $action);
        }

        if ($caseId !== '') {
            $query->where('case_id', $caseId);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        $userOptions = User::query()
            ->select(['id', 'name', 'role'])
            ->when(! $user->hasRole('platform_admin'), fn ($q) => $q->where('org_id', $user->org_id))
            ->orderBy('name')
            ->get();

        $actionOptions = AuditLog::query()
            ->select('action')
            ->when(! $user->hasRole('platform_admin'), fn ($q) => $q->where('org_id', $user->org_id))
            ->distinct()
            ->orderBy('action')
            ->pluck('action')
            ->all();

        return view('admin.audit-logs.index', [
            'logs' => $logs,
            'from' => $from,
            'to' => $to,
            'userId' => $userId,
            'action' => $action,
            'caseId' => $caseId,
            'userOptions' => $userOptions,
            'actionOptions' => $actionOptions,
        ]);
    }
}
