<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display a paginated list of reports relevant to the signed-in reviewer.
     */
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', '');
        $urgent = (string) $request->query('urgent', '');
        $category = (string) $request->query('category', '');
        $subcategory = (string) $request->query('subcategory', '');
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');
        $violationFrom = (string) $request->query('violation_from', '');
        $violationTo = (string) $request->query('violation_to', '');
        $sort = (string) $request->query('sort', 'submitted_desc');

        $query = Report::query()
            ->with(['files'])
            ->withCount('files');

        if (! $request->user()->can('view-all')) {
            $query->where('org_id', $request->user()->org_id);
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($urgent !== '') {
            $query->where('urgent', $urgent === '1');
        }

        if ($category !== '') {
            $query->where('category', $category);
        }

        if ($subcategory !== '') {
            $query->where('subcategory', $subcategory);
        }

        if ($from !== '' && Carbon::hasFormat($from, 'Y-m-d')) {
            $query->whereDate('created_at', '>=', Carbon::createFromFormat('Y-m-d', $from));
        }

        if ($to !== '' && Carbon::hasFormat($to, 'Y-m-d')) {
            $query->whereDate('created_at', '<=', Carbon::createFromFormat('Y-m-d', $to));
        }

        if ($violationFrom !== '' && Carbon::hasFormat($violationFrom, 'Y-m-d')) {
            $query->whereDate('violation_date', '>=', Carbon::createFromFormat('Y-m-d', $violationFrom));
        }

        if ($violationTo !== '' && Carbon::hasFormat($violationTo, 'Y-m-d')) {
            $query->whereDate('violation_date', '<=', Carbon::createFromFormat('Y-m-d', $violationTo));
        }

        $reports = $this->applySorting($query, $sort)
            ->paginate(20)
            ->withQueryString();

        $categoriesMap = ReportCategory::query()
            ->with('subcategories')
            ->orderBy('position')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function (ReportCategory $categoryModel): array {
                $subcategories = $categoryModel->subcategories
                    ->map(fn ($sub) => $sub->name)
                    ->all();

                return [$categoryModel->name => $subcategories];
            })
            ->toArray();

        $subcategoryOptions = $category !== '' ? ($categoriesMap[$category] ?? []) : [];

        return view('dashboard.index', [
            'reports' => $reports,
            'status' => $status,
            'urgent' => $urgent,
            'category' => $category,
            'subcategory' => $subcategory,
            'from' => $from,
            'to' => $to,
            'violationFrom' => $violationFrom,
            'violationTo' => $violationTo,
            'sort' => $sort,
            'categoriesMap' => $categoriesMap,
            'subcategoryOptions' => $subcategoryOptions,
        ]);
    }

    /**
     * Apply sorting preferences to the dashboard query.
     */
    protected function applySorting(Builder $query, string $sort): Builder
    {
        return match ($sort) {
            'submitted_asc' => $query->orderBy('created_at', 'asc'),
            'violation_desc' => $query
                ->orderByRaw('violation_date IS NULL')
                ->orderBy('violation_date', 'desc')
                ->orderBy('created_at', 'desc'),
            'violation_asc' => $query
                ->orderByRaw('violation_date IS NULL')
                ->orderBy('violation_date', 'asc')
                ->orderBy('created_at', 'desc'),
            'category_asc' => $query
                ->orderBy('category')
                ->orderBy('subcategory')
                ->orderBy('created_at', 'desc'),
            'category_desc' => $query
                ->orderBy('category', 'desc')
                ->orderBy('subcategory', 'desc')
                ->orderBy('created_at', 'desc'),
            'subcategory_asc' => $query
                ->orderBy('subcategory')
                ->orderBy('category')
                ->orderBy('created_at', 'desc'),
            'subcategory_desc' => $query
                ->orderBy('subcategory', 'desc')
                ->orderBy('category', 'desc')
                ->orderBy('created_at', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };
    }
}
