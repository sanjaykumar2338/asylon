<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class StaticPageController extends Controller
{
    /**
     * Display the support page.
     */
    public function support(): View
    {
        return $this->renderPageOrFallback('support', 'static.support');
    }

    /**
     * Display the terms page.
     */
    public function terms(): View
    {
        return $this->renderPageOrFallback('terms', 'static.terms');
    }

    /**
     * Display the privacy policy.
     */
    public function privacy(): View
    {
        return $this->renderPageOrFallback('privacy', 'static.privacy');
    }

    protected function renderPageOrFallback(string $slug, string $fallbackView): View
    {
        $page = Page::where('slug', $slug)->where('published', true)->first();

        if ($page) {
            return view('pages.show', [
                'page' => $page,
                'pageTitle' => $page->meta_title ?? $page->title,
            ]);
        }

        $supportEmail = config('asylon.support_email', 'support@asylon.cc');
        $infoEmail = config('asylon.info_email', 'info@asylon.cc');

        return view($fallbackView, [
            'supportEmail' => $supportEmail,
            'infoEmail' => $infoEmail,
            'privacyPolicyUrl' => config('app.privacy_policy_url'),
        ]);
    }
}
