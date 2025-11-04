<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreReportSubcategoryRequest;
use App\Http\Requests\Admin\UpdateReportSubcategoryRequest;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ReportSubcategory;
use Illuminate\Http\RedirectResponse;

class ReportSubcategoryController extends Controller
{
    /**
     * Store a newly created subcategory.
     */
    public function store(StoreReportSubcategoryRequest $request, ReportCategory $reportCategory): RedirectResponse
    {
        $data = $request->validated();
        $data['name'] = trim($data['name']);
        $data['description'] = $data['description'] ?? null;
        $data['position'] = array_key_exists('position', $data) && $data['position'] !== null
            ? (int) $data['position']
            : (($reportCategory->subcategories()->max('position') ?? 0) + 1);

        $reportCategory->subcategories()->create($data);

        return redirect()
            ->route('admin.report-categories.show', $reportCategory)
            ->with('ok', 'Subcategory added.');
    }

    /**
     * Update an existing subcategory.
     */
    public function update(
        UpdateReportSubcategoryRequest $request,
        ReportCategory $reportCategory,
        ReportSubcategory $reportSubcategory
    ): RedirectResponse {
        if ($reportSubcategory->report_category_id !== $reportCategory->id) {
            abort(404);
        }

        $data = $request->validated();
        $data['name'] = trim($data['name']);
        $data['description'] = $data['description'] ?? null;
        $data['position'] = array_key_exists('position', $data) && $data['position'] !== null
            ? (int) $data['position']
            : $reportSubcategory->position;

        $originalName = $reportSubcategory->name;

        $reportSubcategory->fill($data);
        $reportSubcategory->save();

        if ($originalName !== $reportSubcategory->name) {
            Report::query()
                ->where('category', $reportCategory->name)
                ->where('subcategory', $originalName)
                ->update(['subcategory' => $reportSubcategory->name]);
        }

        return redirect()
            ->route('admin.report-categories.show', $reportCategory)
            ->with('ok', 'Subcategory updated.');
    }

    /**
     * Remove the specified subcategory from storage.
     */
    public function destroy(
        ReportCategory $reportCategory,
        ReportSubcategory $reportSubcategory
    ): RedirectResponse {
        if ($reportSubcategory->report_category_id !== $reportCategory->id) {
            abort(404);
        }

        $usageCount = Report::query()
            ->where('category', $reportCategory->name)
            ->where('subcategory', $reportSubcategory->name)
            ->count();

        if ($usageCount > 0) {
            return redirect()
                ->route('admin.report-categories.index')
                ->with('error', 'Cannot delete a subcategory that is used by existing reports.');
        }

        $reportSubcategory->delete();

        return redirect()
            ->route('admin.report-categories.show', $reportCategory)
            ->with('ok', 'Subcategory deleted.');
    }
}
