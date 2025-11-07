<?php

namespace App\Services\Sms;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
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

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->post($this->apiBase . '/messages', $payload);

            if ($response->successful()) {
                $data = $response->json('data') ?? [];

                Log::info('Telnyx SMS queued successfully.', [
                    'to' => $to,
                    'from' => $from,
                    'message_id' => $data['id'] ?? null,
                ]);

                return ['ok' => true, 'data' => $data];
            }

            $body = $response->json();
            Log::error('Telnyx SMS API error.', [
                'to' => $to,
                'status' => $response->status(),
                'body' => $body,
            ]);

            return [
                'error' => 'api_error',
                'status' => $response->status(),
                'body' => $body,
            ];
        } catch (\Throwable $exception) {
            Log::error('Telnyx SMS exception thrown.', [
                'to' => $to,
                'exception' => $exception->getMessage(),
            ]);

            return ['error' => 'exception', 'message' => $exception->getMessage()];
        }
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

    protected function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
