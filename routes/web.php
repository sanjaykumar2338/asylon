<?php

use App\Http\Controllers\Admin\AlertController as AdminAlertController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\ExportController as AdminExportController;
use App\Http\Controllers\Admin\OrgController as AdminOrgController;
use App\Http\Controllers\Admin\ReportCategoryController as AdminReportCategoryController;
use App\Http\Controllers\Admin\ReportSubcategoryController as AdminReportSubcategoryController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TrashReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['setLocale'])->group(function () {
    require base_path('routes/public.php');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:reviewer,security_lead,org_admin,platform_admin')
        ->name('dashboard');

    Route::get('/reviews', [ReviewController::class, 'index'])
        ->middleware('can:review-reports')
        ->name('reviews.index');
    Route::get('/reviews/trash', TrashReportController::class)
        ->middleware('can:review-reports')
        ->name('reviews.trash');
    Route::patch('/reviews/trash/{report}', [TrashReportController::class, 'restore'])
        ->middleware('can:review-reports')
        ->name('reviews.trash.restore');

    Route::post('/reports/{report}/message', [ReviewController::class, 'messageReporter'])
        ->middleware('can:review-reports')
        ->name('reports.message');

    Route::post('/reports/{report}/notes', [ReviewController::class, 'storeNote'])
        ->middleware('can:review-reports')
        ->name('reports.notes.store');

    Route::patch('/reports/{report}/status', [ReviewController::class, 'updateStatus'])
        ->middleware('can:review-reports')
        ->name('reports.status');

    Route::get('/reports/{report}', [ReviewController::class, 'show'])
        ->middleware('can:review-reports')
        ->name('reports.show');

    Route::get('/reports/{report}/edit', [ReviewController::class, 'edit'])
        ->middleware('can:review-reports')
        ->name('reports.edit');
    Route::put('/reports/{report}', [ReviewController::class, 'update'])
        ->middleware('can:review-reports')
        ->name('reports.update');
    Route::delete('/reports/{report}', [ReviewController::class, 'destroy'])
        ->middleware('can:review-reports')
        ->name('reports.destroy');

    Route::get('/reports/{report}/files/{file}', [ReviewController::class, 'downloadFile'])
        ->middleware(['can:review-reports', 'signed'])
        ->name('reports.files.show');
    Route::get('/reports/{report}/files/{file}/preview', [ReviewController::class, 'previewFile'])
        ->middleware(['can:review-reports', 'signed'])
        ->name('reports.files.preview');

    Route::middleware(['can:manage-org'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('orgs', AdminOrgController::class);
        Route::resource('users', AdminUserController::class);
        Route::resource('alerts', AdminAlertController::class)
            ->parameters(['alerts' => 'alert']);
        Route::post('report-categories/{report_category}/toggle-visibility', [AdminReportCategoryController::class, 'toggleVisibility'])
            ->name('report-categories.toggle-visibility')
            ->middleware('can:manage-categories');
        Route::resource('report-categories', AdminReportCategoryController::class);
        Route::get('reports/export', [AdminExportController::class, 'reports'])
            ->name('reports.export');
        Route::post('report-categories/{report_category}/subcategories', [AdminReportSubcategoryController::class, 'store'])
            ->name('report-categories.subcategories.store');
        Route::put('report-categories/{report_category}/subcategories/{report_subcategory}', [AdminReportSubcategoryController::class, 'update'])
            ->name('report-categories.subcategories.update');
        Route::delete('report-categories/{report_category}/subcategories/{report_subcategory}', [AdminReportSubcategoryController::class, 'destroy'])
            ->name('report-categories.subcategories.destroy');
        Route::get('analytics', [AdminAnalyticsController::class, 'index'])
            ->name('analytics');

        Route::middleware('can:manage-platform')->group(function () {
            Route::get('settings', [AdminSettingsController::class, 'edit'])
                ->name('settings.edit');
            Route::post('settings', [AdminSettingsController::class, 'update'])
                ->name('settings.update');
        });
    });

    Route::middleware('can:manage-org')->group(function () {
        // Future admin routes go here.
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

