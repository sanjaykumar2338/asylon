<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class StaticPageController extends Controller
{
    /**
     * Display the support page.
     */
    public function support(): View
    {
        $supportEmail = config('asylon.support_email', 'support@asylon.cc');
        $infoEmail = config('asylon.info_email', 'info@asylon.cc');

        return view('static.support', [
            'supportEmail' => $supportEmail,
            'infoEmail' => $infoEmail,
        ]);
    }

    /**
     * Display the terms page.
     */
    public function terms(): View
    {
        $supportEmail = config('asylon.support_email', 'support@asylon.cc');
        $infoEmail = config('asylon.info_email', 'info@asylon.cc');

        return view('static.terms', [
            'supportEmail' => $supportEmail,
            'infoEmail' => $infoEmail,
            'privacyPolicyUrl' => config('app.privacy_policy_url'),
        ]);
    }

    /**
     * Display the privacy policy.
     */
    public function privacy(): View
    {
        $supportEmail = config('asylon.support_email', 'support@asylon.cc');
        $infoEmail = config('asylon.info_email', 'info@asylon.cc');

        return view('static.privacy', [
            'supportEmail' => $supportEmail,
            'infoEmail' => $infoEmail,
        ]);
    }
}
