<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display the settings form.
     */
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'sms_enabled' => $this->boolSetting('sms_enabled', config('services.telnyx.sms_enabled', true)),
            'telnyx_api_key' => Setting::get('telnyx_api_key', config('services.telnyx.key')),
            'telnyx_from_number' => Setting::get('telnyx_from_number', config('services.telnyx.from')),
            'telnyx_alpha_sender' => Setting::get('telnyx_alpha_sender', config('services.telnyx.alpha')),
            'telnyx_enable_alpha' => $this->boolSetting('telnyx_enable_alpha', config('services.telnyx.enable_alpha', false)),
            'admin_2fa_enabled' => $this->boolSetting('admin_2fa_enabled', false),
            'admin_2fa_email' => $this->boolSetting('admin_2fa_email', true),
            'admin_2fa_sms' => $this->boolSetting('admin_2fa_sms', false),
        ]);
    }

    /**
     * Persist settings updates.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'sms_enabled' => ['nullable', 'boolean'],
            'telnyx_api_key' => ['nullable', 'string', 'max:255'],
            'telnyx_from_number' => ['nullable', 'string', 'max:30'],
            'telnyx_alpha_sender' => ['nullable', 'string', 'max:11'],
            'telnyx_enable_alpha' => ['nullable', 'boolean'],
            'admin_2fa_enabled' => ['nullable', 'boolean'],
            'admin_2fa_email' => ['nullable', 'boolean'],
            'admin_2fa_sms' => ['nullable', 'boolean'],
        ]);

        Setting::set('sms_enabled', $request->boolean('sms_enabled'));
        Setting::set('telnyx_enable_alpha', $request->boolean('telnyx_enable_alpha'));
        Setting::set('admin_2fa_enabled', $request->boolean('admin_2fa_enabled'));
        Setting::set('admin_2fa_email', $request->boolean('admin_2fa_email'));
        Setting::set('admin_2fa_sms', $request->boolean('admin_2fa_sms'));

        $apiKeyInput = trim((string) $request->input('telnyx_api_key', ''));
        Setting::set('telnyx_api_key', $apiKeyInput === '' ? null : $apiKeyInput);

        $from = trim((string) $request->input('telnyx_from_number', ''));
        Setting::set('telnyx_from_number', $from !== '' ? $from : null);

        $alpha = trim((string) $request->input('telnyx_alpha_sender', ''));
        Setting::set('telnyx_alpha_sender', $alpha !== '' ? strtoupper($alpha) : null);

        return back()->with('ok', 'Settings updated.');
    }

    protected function boolSetting(string $key, bool $default = false): bool
    {
        $value = Setting::get($key);

        if ($value === null) {
            return $default;
        }

        return is_bool($value) ? $value : filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
