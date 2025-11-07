<?php

namespace App\Services\Sms;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class TelnyxSmsService
{
    protected string $apiBase = 'https://api.telnyx.com/v2';

    /**
     * Send an SMS message via Telnyx.
     *
     * @return array<string, mixed>
     */
    public function send(string $toE164, string $text): array
    {
        $to = trim($toE164);

        if ($to === '' || $text === '') {
            Log::warning('Skipped Telnyx SMS due to missing to/text payload.', [
                'to' => $toE164,
            ]);

            return ['skipped' => true, 'reason' => 'invalid_payload'];
        }

        if (! $this->smsEnabled()) {
            Log::info('SMS delivery disabled in settings; skipping Telnyx send.', [
                'to' => $to,
            ]);

            return ['skipped' => true, 'reason' => 'disabled'];
        }

        $apiKey = $this->apiKey();
        $from = $this->pickFrom($to);
        $messagingProfileId = $this->messagingProfileId();

        if (! $apiKey) {
            Log::warning('Telnyx SMS not configured. Missing API key.', [
                'to' => $to,
            ]);

            return ['error' => 'not_configured'];
        }

        if (! $from) {
            // pickFrom already logged the reason.
            return ['skipped' => true, 'reason' => 'sender_unavailable'];
        }

        $payload = [
            'from' => $from,
            'to' => $to,
            'text' => $text,
        ];


        Log::error('Telnyx payload', [
            'payload' =>  $payload
        ]);


        if ($messagingProfileId) {
            $payload['messaging_profile_id'] = $messagingProfileId;
        }

        Log::debug('Telnyx SMS payload prepared.', [
            'payload' => $payload,
        ]);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiBase . '/messages',
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
                'Authorization: ' . 'Bearer '.$apiKey,
            ],
        ]);

        $responseBody = curl_exec($curl);
        $curlError = curl_error($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($responseBody === false) {
            Log::error('Telnyx SMS cURL error.', [
                'to' => $to,
                'error' => $curlError,
            ]);

            return ['error' => 'curl_error', 'message' => $curlError];
        }

        $decoded = json_decode($responseBody, true);

        if ($status >= 200 && $status < 300) {
            $data = $decoded['data'] ?? [];

            Log::info('Telnyx SMS queued successfully.', [
                'to' => $to,
                'from' => $from,
                'message_id' => $data['id'] ?? null,
            ]);

            return ['ok' => true, 'data' => $data];
        }

        Log::error('Telnyx SMS API error.', [
            'to' => $to,
            'status' => $status,
            'body' => $decoded,
        ]);

        return [
            'error' => 'api_error',
            'status' => $status,
            'body' => $decoded,
        ];
    }

    protected function apiKey(): ?string
    {
        return Setting::get('telnyx_api_key') ?: config('services.telnyx.key');
    }

    protected function fromNumber(): ?string
    {
        $value = Setting::get('telnyx_from_number') ?: config('services.telnyx.from');

        return $value ? trim($value) : null;
    }

    protected function alphaSender(): ?string
    {
        $value = Setting::get('telnyx_alpha_sender') ?: config('services.telnyx.alpha');

        return $value ? strtoupper(trim($value)) : null;
    }

    protected function alphaEnabled(): bool
    {
        $value = Setting::get('telnyx_enable_alpha');

        if ($value === null) {
            return (bool) config('services.telnyx.enable_alpha', false);
        }

        return $this->toBool($value);
    }

    protected function smsEnabled(): bool
    {
        $value = Setting::get('sms_enabled');

        if ($value === null) {
            return (bool) config('services.telnyx.sms_enabled', true);
        }

        return $this->toBool($value);
    }

    /**
     * Choose the appropriate sender for the destination number.
     */
    protected function pickFrom(string $toE164): ?string
    {
        if (! str_starts_with($toE164, '+1') && $this->alphaEnabled() && $this->alphaSender()) {
            return $this->alphaSender();
        }

        return $this->fromNumber();
    }

    protected function messagingProfileId(): ?string
    {
        $value = Setting::get('telnyx_messaging_profile_id')
            ?: config('services.telnyx.messaging_profile_id');

        return $value ? trim($value) : null;
    }

    protected function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
