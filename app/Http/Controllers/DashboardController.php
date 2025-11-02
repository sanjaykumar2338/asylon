<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display a paginated list of reports relevant to the signed-in reviewer.
     */
    public function index(Request $request): View
    {
        $query = Report::query()
            ->with(['files'])
            ->withCount('files')
            ->latest();

        if (! $request->user()->can('view-all')) {
            $query->where('org_id', $request->user()->org_id);
        }

        $reports = $query->paginate(20);

        return view('dashboard.index', compact('reports'));
    }
}
