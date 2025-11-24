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
        $supportEmail = config('asylon.support_email', 'support@asylon.com');

        return view('static.support', [
            'supportEmail' => $supportEmail,
        ]);
    }

    /**
     * Display the terms page.
     */
    public function terms(): View
    {
        $supportEmail = config('asylon.support_email', 'support@asylon.com');

        return view('static.terms', [
            'supportEmail' => $supportEmail,
            'privacyPolicyUrl' => config('app.privacy_policy_url'),
        ]);
    }
}
