<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display a paginated list of reports relevant to the signed-in reviewer.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $reportsScope = Report::query();

        if (! $user->can('view-all')) {
            $reportsScope->where('org_id', $user->org_id);
        }

        $totalReports = (clone $reportsScope)->count();
        $openReports = (clone $reportsScope)->where('status', 'open')->count();
        $urgentReports = (clone $reportsScope)->where('urgent', true)->count();

        $userQuery = User::query();
        if (! $user->hasRole('platform_admin')) {
            $userQuery->where('org_id', $user->org_id);
        }
        $totalUsers = $userQuery->count();

        return view('dashboard.index', [
            'stats' => [
                'totalReports' => $totalReports,
                'openReports' => $openReports,
                'urgentReports' => $urgentReports,
                'totalUsers' => $totalUsers,
            ],
        ]);
    }
}
