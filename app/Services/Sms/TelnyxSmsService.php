<?php

namespace App\Services\Sms;

use App\Models\Setting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Support\TemplateRenderer;
use Throwable;

class TelnyxSmsService
{
    protected string $apiBase = 'https://api.telnyx.com/v2';
    protected int $defaultTimeout = 8;
    protected int $defaultConnectTimeout = 4;

    /**
     * Send an SMS message via Telnyx.
     *
     * @return array<string, mixed>
     */
    public function send(string $toE164, string $text): array
    {
        $to = $this->normalizeRecipient($toE164);

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
        $from = $this->normalizeSender($this->pickFrom($to));
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

        $text = TemplateRenderer::ensureSmsCompliance($text);

        $payload = [
            'from' => $from,
            'to' => $to,
            'text' => $text,
        ];

        if ($messagingProfileId) {
            $payload['messaging_profile_id'] = $messagingProfileId;
        }

        Log::debug('Telnyx SMS payload prepared.', [
            'payload' => $payload,
        ]);

        $request = Http::timeout($this->requestTimeout())
            ->connectTimeout($this->connectTimeout())
            ->withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
            ])
            ->asJson();

        if ($this->shouldSkipSslVerification()) {
            $request->withoutVerifying();
            Log::warning('Telnyx SMS request is skipping SSL verification. Configure CA bundle for production.', []);
        }

        try {
            $response = $request->post($this->apiBase.'/messages', $payload);
        } catch (ConnectionException $e) {
            Log::error('Telnyx SMS connection error.', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);

            return ['error' => 'connection_error', 'message' => $e->getMessage()];
        } catch (Throwable $e) {
            Log::error('Telnyx SMS unexpected error.', [
                'to' => $to,
                'exception' => $e,
            ]);

            return ['error' => 'unexpected_error', 'message' => $e->getMessage()];
        }

        if ($response->successful()) {
            $data = $response->json('data', []);

            Log::info('Telnyx SMS queued successfully.', [
                'to' => $to,
                'from' => $from,
                'message_id' => $data['id'] ?? null,
            ]);

            return ['ok' => true, 'data' => $data];
        }

        Log::error('Telnyx SMS API error.', [
            'to' => $to,
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        return [
            'error' => 'api_error',
            'status' => $response->status(),
            'body' => $response->json(),
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

    protected function shouldSkipSslVerification(): bool
    {
        $value = Setting::get('telnyx_skip_ssl_verify');

        if ($value === null) {
            return (bool) config('services.telnyx.skip_ssl_verify', app()->environment('local'));
        }

        return $this->toBool($value);
    }

    protected function requestTimeout(): int
    {
        $timeout = Setting::get('telnyx_timeout') ?? config('services.telnyx.timeout', $this->defaultTimeout);

        $timeout = (int) $timeout;

        return $timeout > 0 ? $timeout : $this->defaultTimeout;
    }

    protected function connectTimeout(): int
    {
        $timeout = Setting::get('telnyx_connect_timeout') ?? config('services.telnyx.connect_timeout', $this->defaultConnectTimeout);

        $timeout = (int) $timeout;

        return $timeout > 0 ? $timeout : $this->defaultConnectTimeout;
    }

    protected function normalizeSender(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (preg_match('/[a-z]/i', $value)) {
            return strtoupper($value);
        }

        return str_starts_with($value, '+') ? $value : '+'.$value;
    }

    protected function normalizeRecipient(string $value): string
    {
        $number = trim($value);

        if ($number === '') {
            return $number;
        }

        return str_starts_with($number, '+') ? $number : '+'.$number;
    }

    protected function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
