<?php

namespace App\Support;

use App\Models\Setting;
use App\Models\User;
use App\Notifications\TwoFactorCodeNotification;
use App\Services\Sms\TelnyxSmsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TwoFactorService
{
    public const SESSION_KEY = 'two_factor_user_id';

    public function isEnabledFor(User $user): bool
    {
        if (! $user->role) {
            return false;
        }

        if (! $this->setting('admin_2fa_enabled', false)) {
            return false;
        }

        return in_array($user->role, ['platform_admin', 'executive_admin'], true)
            || (property_exists($user, 'admin') && (bool) $user->admin === true);
    }

    public function sendCode(User $user): void
    {
        $code = $this->generateCode();
        $user->forceFill([
            'two_factor_code' => $code,
            'two_factor_expires_at' => Carbon::now()->addMinutes(10),
        ])->save();

        if ($this->setting('admin_2fa_email', true)) {
            Log::info('2FA email dispatching', [
                'user_id' => $user->getKey(),
                'email' => $user->email,
            ]);

            try {
                $user->notify(new TwoFactorCodeNotification($code));
            } catch (\Throwable $e) {
                Log::error('2FA email failed to dispatch', [
                    'user_id' => $user->getKey(),
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            Log::info('2FA email delivery disabled via settings', [
                'user_id' => $user->getKey(),
            ]);
        }

        if ($this->setting('admin_2fa_sms', false)) {
            $phone = method_exists($user, 'getAttribute') ? (string) $user->getAttribute('phone') : '';
            $phone = $phone ?: (method_exists($user, 'getAttribute') ? (string) $user->getAttribute('contact_phone') : '');

            if ($phone !== '') {
                try {
                    Log::info('2FA SMS dispatching', [
                        'user_id' => $user->getKey(),
                        'phone' => $phone,
                    ]);

                    app(TelnyxSmsService::class)->send($phone, __('Your login code is :code', ['code' => $code]));
                } catch (\Throwable $e) {
                    Log::warning('Failed to send 2FA SMS', [
                        'user_id' => $user->getKey(),
                        'error' => $e->getMessage(),
                    ]);
                }
            } else {
                Log::warning('2FA SMS skipped - no phone on user', [
                    'user_id' => $user->getKey(),
                ]);
            }
        } else {
            Log::info('2FA SMS delivery disabled via settings', [
                'user_id' => $user->getKey(),
            ]);
        }
    }

    public function verify(User $user, string $input): bool
    {
        $code = trim($input);

        if ($user->two_factor_code === null || $user->two_factor_expires_at === null) {
            return false;
        }

        if (Carbon::now()->greaterThan($user->two_factor_expires_at)) {
            return false;
        }

        return hash_equals($user->two_factor_code, $code);
    }

    public function clear(User $user): void
    {
        $user->forceFill([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ])->save();
    }

    protected function generateCode(): string
    {
        return (string) random_int(100000, 999999);
    }

    protected function setting(string $key, bool $default = false): bool
    {
        $value = Setting::get($key);

        if ($value === null) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
