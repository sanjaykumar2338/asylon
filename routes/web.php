<?php

use App\Http\Controllers\Admin\AlertController as AdminAlertController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\ExportController as AdminExportController;
use App\Http\Controllers\Admin\OrgController as AdminOrgController;
use App\Http\Controllers\Admin\ReportCategoryController as AdminReportCategoryController;
use App\Http\Controllers\Admin\ReportSubcategoryController as AdminReportSubcategoryController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TrashReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/report');

Route::get('/report/student', [ReportController::class, 'createStudent'])->name('report.student');
Route::post('/report/student', [ReportController::class, 'storeStudent'])->name('report.student.store');
Route::get('/report/employee', [ReportController::class, 'createEmployee'])->name('report.employee');
Route::post('/report/employee', [ReportController::class, 'storeEmployee'])->name('report.employee.store');

Route::get('/report', [ReportController::class, 'create'])->name('report.create');
Route::get('/report/{org_code}', [ReportController::class, 'createByCode'])->name('report.by_code');
Route::post('/report', [ReportController::class, 'store'])->middleware('throttle:report-submit')->name('report.store');
Route::get('/report/thanks/{id}', [ReportController::class, 'thanks'])->name('report.thanks');

if (app()->environment('local')) {
    Route::get('/dev/telnyx-test-sms', function (Request $request) {
        $apiKey = $request->query('key', config('services.telnyx.key'));

        if (! $apiKey) {
            return response()->json([
                'ok' => false,
                'error' => 'missing_api_key',
                'message' => 'Set TELNYX_API_KEY or provide ?key=YOUR_API_KEY',
            ], 422);
        }

        $ensurePlus = static function (?string $value, string $fallback): string {
            $number = $value ?? $fallback;
            $number = trim($number);
            return str_starts_with($number, '+') ? $number : '+'.$number;
        };

        $payload = [
            'from' => $ensurePlus($request->query('from'), '+12143937242'),
            'to' => $ensurePlus($request->query('to'), '+917814976130'),
            'text' => $request->query('text', 'Hello from Telnyx!'),
        ];

        if ($request->filled('messaging_profile_id')) {
            $payload['messaging_profile_id'] = $request->query('messaging_profile_id');
        } elseif ($profileId = config('services.telnyx.messaging_profile_id')) {
            $payload['messaging_profile_id'] = $profileId;
        }

        Log::debug('Manual Telnyx SMS test payload.', $payload);

        $skipSslVerification = $request->boolean('insecure', true);

        $curl = curl_init();

        $curlOptions = [
            CURLOPT_URL => 'https://api.telnyx.com/v2/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer '.$apiKey,
            ],
        ];

        if ($skipSslVerification) {
            $curlOptions[CURLOPT_SSL_VERIFYPEER] = false;
            $curlOptions[CURLOPT_SSL_VERIFYHOST] = 0;
            Log::warning('Manual Telnyx SMS test is skipping SSL verification. For production, install proper CA certs.', []);
        }

        curl_setopt_array($curl, $curlOptions);

        $responseBody = curl_exec($curl);
        $curlError = curl_error($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE) ?: 0;
        curl_close($curl);

        if ($responseBody === false) {
            Log::error('Manual Telnyx SMS test cURL failure.', [
                'error' => $curlError,
            ]);

            return response()->json([
                'ok' => false,
                'error' => 'curl_error',
                'message' => $curlError,
            ], 500);
        }

        $decoded = json_decode($responseBody, true);

        $success = $status >= 200 && $status < 300;

        Log::info('Manual Telnyx SMS test completed.', [
            'status' => $status,
            'success' => $success,
        ]);

        return response()->json([
            'ok' => $success,
            'status' => $status,
            'payload' => $payload,
            'response' => $decoded,
        ], $success ? 200 : 500);
    })->name('dev.telnyx-test-sms');
}

Route::get('/followup', [FollowUpController::class, 'entry'])->name('followup.entry');
Route::post('/followup', [FollowUpController::class, 'redirectFromEntry'])->name('followup.redirect');

Route::get('/followup/{token}', [FollowUpController::class, 'show'])->name('followup.show');
Route::post('/followup/{token}', [FollowUpController::class, 'storeMessage'])
    ->middleware('throttle:chat-post')
    ->name('followup.message');
Route::get('/followup/{token}/attachments/{file}/preview', [FollowUpController::class, 'previewAttachment'])
    ->name('followup.attachments.preview');
Route::get('/followup/{token}/attachments/{file}/download', [FollowUpController::class, 'downloadAttachment'])
    ->name('followup.attachments.download');

// Legacy chat URLs (kept for backward compatibility)
Route::get('/chat/{token}', [FollowUpController::class, 'show'])->name('chat.thread');
Route::post('/chat/{token}', [FollowUpController::class, 'storeMessage'])
    ->middleware('throttle:chat-post')
    ->name('chat.post');
Route::get('/report/{token}/attachments/{file}/preview', [FollowUpController::class, 'previewAttachment'])
    ->name('report.attachments.preview');
Route::get('/report/{token}/attachments/{file}/download', [FollowUpController::class, 'downloadAttachment'])
    ->name('report.attachments.download');

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

