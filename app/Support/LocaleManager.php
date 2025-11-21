<?php

namespace App\Support;

use App\Models\Org;
use Illuminate\Support\Facades\Session;

class LocaleManager
{
    /**
     * Apply the organization's default locale when no manual preference is stored in the session.
     */
    public static function applyOrgLocale(?Org $org): void
    {
        if (! $org || Session::has('asylon.locale')) {
            return;
        }

        $locale = strtolower((string) ($org->default_locale ?? ''));

        if ($locale === '') {
            return;
        }

        $supported = array_keys(config('asylon.languages', []));

        if (! in_array($locale, $supported, true)) {
            return;
        }

        app()->setLocale($locale);
    }
}
