<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreReportCategoryRequest;
use App\Http\Requests\Admin\UpdateReportCategoryRequest;
use App\Models\Report;
use App\Models\ReportCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReportCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $categories = ReportCategory::query()
            ->withCount('subcategories')
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        return view('admin.report-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $category = new ReportCategory([
            'position' => (ReportCategory::max('position') ?? 0) + 1,
        ]);

        return view('admin.report-categories.create', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReportCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['name'] = trim($data['name']);
        $data['description'] = $data['description'] ?? null;
        $data['position'] = array_key_exists('position', $data) && $data['position'] !== null
            ? (int) $data['position']
            : ((ReportCategory::max('position') ?? 0) + 1);

        ReportCategory::create($data);

        return redirect()
            ->route('admin.report-categories.index')
            ->with('ok', 'Category created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ReportCategory $reportCategory): View
    {
        $reportCategory->load(['subcategories' => fn ($query) => $query->orderBy('position')->orderBy('name')]);

        return view('admin.report-categories.show', [
            'category' => $reportCategory,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReportCategory $reportCategory): View
    {
        return view('admin.report-categories.edit', [
            'category' => $reportCategory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReportCategoryRequest $request, ReportCategory $reportCategory): RedirectResponse
    {
        $data = $request->validated();
        $data['name'] = trim($data['name']);
        $data['description'] = $data['description'] ?? null;
        $data['position'] = array_key_exists('position', $data) && $data['position'] !== null
            ? (int) $data['position']
            : $reportCategory->position;

        $originalName = $reportCategory->name;

        $reportCategory->fill($data);
        $reportCategory->save();

        if ($originalName !== $reportCategory->name) {
            Report::query()
                ->where('category', $originalName)
                ->update(['category' => $reportCategory->name]);
        }

        return redirect()
            ->route('admin.report-categories.index')
            ->with('ok', 'Category updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReportCategory $reportCategory): RedirectResponse
    {
        $usageCount = Report::query()
            ->where('category', $reportCategory->name)
            ->count();

        if ($usageCount > 0) {
            return redirect()
                ->route('admin.report-categories.index')
                ->with('error', 'Cannot delete a category that is used by existing reports.');
        }

        $reportCategory->delete();

        return redirect()
            ->route('admin.report-categories.index')
            ->with('ok', 'Category deleted.');
    }
}
