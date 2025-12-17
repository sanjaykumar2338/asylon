<?php

use App\Http\Controllers\Admin\AlertController as AdminAlertController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\ExportController as AdminExportController;
use App\Http\Controllers\Admin\DataDeletionAdminController as AdminDataDeletionAdminController;
use App\Http\Controllers\Admin\OrgController as AdminOrgController;
use App\Http\Controllers\Admin\ReportCategoryController as AdminReportCategoryController;
use App\Http\Controllers\Admin\ReportSubcategoryController as AdminReportSubcategoryController;
use App\Http\Controllers\Admin\NotificationTemplateController as AdminNotificationTemplateController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\MenuItemController as AdminMenuItemController;
use App\Http\Controllers\Admin\BlogCategoryController as AdminBlogCategoryController;
use App\Http\Controllers\Admin\BlogPostController as AdminBlogPostController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\Admin\EscalationRuleController as AdminEscalationRuleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrgSettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TrashReportController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as VerifyCsrfTokenMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(['setLocale'])->group(function () {
    require base_path('routes/public.php');
});

// Public Stripe webhook endpoint
Route::post('/stripe/webhook', StripeWebhookController::class)
    ->name('stripe.webhook')
    ->withoutMiddleware([VerifyCsrfTokenMiddleware::class]);

Route::get('/get-started', [SignupController::class, 'showForm'])->name('signup.show');
Route::post('/get-started', [SignupController::class, 'store'])->name('signup.store');
Route::get('/welcome', [SignupController::class, 'welcome'])->name('welcome');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/org-suspended', fn () => view('org.suspended'))->name('org.suspended');
    Route::get('/settings/organization', [OrgSettingsController::class, 'edit'])
        ->name('settings.organization.edit');
    Route::post('/settings/organization', [OrgSettingsController::class, 'update'])
        ->name('settings.organization.update');

    Route::middleware('role:platform_admin,executive_admin,org_admin')->group(function () {
        Route::get('/billing/choose-plan', [BillingController::class, 'choosePlan'])
            ->name('billing.choose_plan');

        Route::post('/billing/checkout', [BillingController::class, 'createCheckout'])
            ->name('billing.checkout');

        Route::get('/billing/success', [BillingController::class, 'success'])
            ->name('billing.success');

        Route::get('/billing/cancel', [BillingController::class, 'cancel'])
            ->name('billing.cancel');

        Route::get('/settings/billing', [BillingController::class, 'settings'])
            ->name('billing.settings');

        Route::post('/billing/portal', [BillingController::class, 'createPortalSession'])
            ->name('billing.portal');
    });

    Route::middleware('role:platform_admin')->prefix('platform')->name('platform.')->group(function () {
        Route::get('organizations', [\App\Http\Controllers\Platform\OrganizationController::class, 'index'])
            ->name('organizations.index');
        Route::get('organizations/{org}', [\App\Http\Controllers\Platform\OrganizationController::class, 'show'])
            ->name('organizations.show');
        Route::post('organizations/{org}/update-plan', [\App\Http\Controllers\Platform\OrganizationController::class, 'updatePlan'])
            ->name('organizations.update_plan');
        Route::post('organizations/{org}/update-status', [\App\Http\Controllers\Platform\OrganizationController::class, 'updateStatus'])
            ->name('organizations.update_status');
        Route::get('plans', [\App\Http\Controllers\Platform\PlanController::class, 'index'])
            ->name('plans.index');
        Route::get('plans/{plan}/prices', [\App\Http\Controllers\Platform\PlanController::class, 'editPrices'])
            ->name('plans.prices.edit');
        Route::put('plans/{plan}/prices', [\App\Http\Controllers\Platform\PlanController::class, 'updatePrices'])
            ->name('plans.prices.update');
        Route::get('billing/subscriptions', [\App\Http\Controllers\Platform\SubscriptionController::class, 'index'])
            ->name('billing.subscriptions.index');
        Route::get('billing/subscriptions/{org}', [\App\Http\Controllers\Platform\SubscriptionController::class, 'show'])
            ->name('billing.subscriptions.show');
        Route::post('billing/subscriptions/{org}/plan', [\App\Http\Controllers\Platform\SubscriptionController::class, 'changePlan'])
            ->name('billing.subscriptions.plan');
        Route::post('billing/subscriptions/{org}/status', [\App\Http\Controllers\Platform\SubscriptionController::class, 'overrideStatus'])
            ->name('billing.subscriptions.status');
        Route::post('billing/subscriptions/{org}/sync', [\App\Http\Controllers\Platform\SubscriptionController::class, 'sync'])
            ->name('billing.subscriptions.sync');
        Route::post('billing/subscriptions/{org}/cancel', [\App\Http\Controllers\Platform\SubscriptionController::class, 'cancel'])
            ->name('billing.subscriptions.cancel');
        Route::post('billing/subscriptions/{org}/resume', [\App\Http\Controllers\Platform\SubscriptionController::class, 'resume'])
            ->name('billing.subscriptions.resume');
        Route::get('billing/revenue', [\App\Http\Controllers\Platform\RevenueController::class, 'index'])
            ->name('billing.revenue');
    });

    Route::middleware(['active-subscription'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->middleware('role:reviewer,security_lead,org_admin,platform_admin,executive_admin')
            ->name('dashboard');

        Route::get('/reports/all', [ReviewController::class, 'index'])
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

        Route::get('/reports/export', [AdminExportController::class, 'reports'])
            ->middleware('can:review-reports')
            ->name('reports.export.list');

        Route::get('/reports/export/pdf', [AdminExportController::class, 'reportsPdf'])
            ->middleware('can:review-reports')
            ->name('reports.export.list.pdf');

        Route::get('/reports/{report}/export/csv', [AdminExportController::class, 'reportCsv'])
            ->middleware('can:review-reports')
            ->name('reports.export.csv');

        Route::get('/reports/{report}/export/pdf', [AdminExportController::class, 'reportPdf'])
            ->middleware('can:review-reports')
            ->name('reports.export.pdf');

        Route::get('/reports/{report}/export/audit', [AdminExportController::class, 'auditPacket'])
            ->middleware('can:review-reports')
            ->name('reports.export.audit');

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
            Route::resource('risk-keywords', \App\Http\Controllers\Admin\RiskKeywordController::class)
            ->except(['create', 'edit', 'show']);
        Route::post('report-categories/{report_category}/toggle-visibility', [AdminReportCategoryController::class, 'toggleVisibility'])
            ->name('report-categories.toggle-visibility')
            ->middleware('can:manage-categories');
        Route::resource('report-categories', AdminReportCategoryController::class);
        Route::resource('escalation-rules', AdminEscalationRuleController::class)->except(['show']);
        Route::get('reports/export', [AdminExportController::class, 'reports'])
            ->name('reports.export');
        Route::get('audit-logs', [AdminAuditLogController::class, 'index'])
            ->middleware('role:platform_admin,executive_admin')
            ->name('audit-logs.index');
        Route::middleware('can:manage-data-requests')->group(function () {
            Route::get('data-requests', [AdminDataDeletionAdminController::class, 'index'])->name('data_requests.index');
            Route::get('data-requests/{dataRequest}', [AdminDataDeletionAdminController::class, 'show'])->name('data_requests.show');
            Route::post('data-requests/{dataRequest}/status', [AdminDataDeletionAdminController::class, 'updateStatus'])->name('data_requests.update_status');
            Route::post('data-requests/from-case/{report}', [AdminDataDeletionAdminController::class, 'storeFromCase'])->name('data_requests.from_case');
        });
        Route::post('report-categories/{report_category}/subcategories', [AdminReportSubcategoryController::class, 'store'])
            ->name('report-categories.subcategories.store');
        Route::put('report-categories/{report_category}/subcategories/{report_subcategory}', [AdminReportSubcategoryController::class, 'update'])
            ->name('report-categories.subcategories.update');
        Route::delete('report-categories/{report_category}/subcategories/{report_subcategory}', [AdminReportSubcategoryController::class, 'destroy'])
            ->name('report-categories.subcategories.destroy');
        Route::get('analytics', [AdminAnalyticsController::class, 'index'])
            ->name('analytics');
        Route::get('notifications/templates', [AdminNotificationTemplateController::class, 'edit'])
            ->name('notifications.templates.edit');
        Route::post('notifications/templates', [AdminNotificationTemplateController::class, 'update'])
            ->name('notifications.templates.update');
        Route::resource('pages', AdminPageController::class)->except(['show']);
        Route::resource('menus', AdminMenuController::class)->except(['show']);
        Route::post('menus/{menu}/items/reorder', [AdminMenuItemController::class, 'reorder'])
            ->name('menus.items.reorder');
        Route::post('menus/{menu}/items', [AdminMenuItemController::class, 'store'])
            ->name('menus.items.store');
        Route::put('menus/{menu}/items/{menuItem}', [AdminMenuItemController::class, 'update'])
            ->name('menus.items.update');
        Route::delete('menus/{menu}/items/{menuItem}', [AdminMenuItemController::class, 'destroy'])
            ->name('menus.items.destroy');
            Route::resource('blog-categories', AdminBlogCategoryController::class)->except(['create', 'edit', 'show']);
            Route::resource('blog-posts', AdminBlogPostController::class)->parameters(['blog-posts' => 'blog_post']);

            Route::middleware('can:manage-platform')->group(function () {
                Route::get('settings', [AdminSettingsController::class, 'edit'])
                    ->name('settings.edit');
                Route::post('settings', [AdminSettingsController::class, 'update'])
                    ->name('settings.update');
            });
        });

        Route::get('/notifications', [NotificationController::class, 'index'])
            ->name('notifications.index');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])
            ->name('notifications.markAllRead');
        Route::post('/notifications/{notificationId}/mark-read', [NotificationController::class, 'markRead'])
            ->name('notifications.markRead');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

require __DIR__.'/auth.php';

// Catch-all for published pages by slug (placed after defined routes to avoid conflicts).
Route::get('/{slug}', [PageController::class, 'resolve'])
    ->where('slug', '[A-Za-z0-9\\-]+')
    ->name('pages.resolve');

// Legacy redirects
if (file_exists(__DIR__.'/legacy.php')) {
    require __DIR__.'/legacy.php';
}
