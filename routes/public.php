<?php

use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StaticPageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/report');
Route::get('/support', [StaticPageController::class, 'support'])->name('support');
Route::get('/privacy', [StaticPageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [StaticPageController::class, 'terms'])->name('terms');
Route::view('/brand-info', 'public.brand_info')->name('brand.sms.info');
Route::view('/privacy-and-anonymity', 'static.privacy_anonymity')->name('privacy.anonymity');
Route::view('/security-overview', 'static.security_overview')->name('security.overview');
Route::view('/sms-opt-in', 'public.sms_opt_in')->name('sms.opt_in');
Route::view('/sms-opt-in-example', 'public.sms_opt_in_example')->name('sms.opt_in_example');
Route::view('/sms-onboarding-sample', 'public.onboarding_sample')->name('sms.onboarding_sample');

Route::middleware('ultra-private')->group(function () {
    Route::get('/report/student', [ReportController::class, 'createStudent'])->name('report.student');
    Route::post('/report/student', [ReportController::class, 'storeStudent'])->name('report.student.store');
    Route::get('/report/employee', [ReportController::class, 'createEmployee'])->name('report.employee');
    Route::post('/report/employee', [ReportController::class, 'storeEmployee'])->name('report.employee.store');

    Route::get('/report', [ReportController::class, 'create'])->name('report.create');
    Route::get('/report/{org_code}', [ReportController::class, 'createByCode'])->name('report.by_code');
    Route::post('/report', [ReportController::class, 'store'])->middleware('throttle:report-submit')->name('report.store');
    Route::get('/report/thanks/{id}', [ReportController::class, 'thanks'])->name('report.thanks');
});

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

Route::middleware('ultra-private')->group(function () {
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

    Route::get('/chat/{token}', [FollowUpController::class, 'show'])->name('chat.thread');
    Route::post('/chat/{token}', [FollowUpController::class, 'storeMessage'])
        ->middleware('throttle:chat-post')
        ->name('chat.post');
    Route::get('/report/{token}/attachments/{file}/preview', [FollowUpController::class, 'previewAttachment'])
        ->name('report.attachments.preview');
    Route::get('/report/{token}/attachments/{file}/download', [FollowUpController::class, 'downloadAttachment'])
        ->name('report.attachments.download');
});
