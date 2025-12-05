<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::where('slug', $slug)->where('published', true)->firstOrFail();
        $seo = \App\Models\SeoPage::where('slug', $slug)->first();

        return view('pages.show', compact('page', 'seo'));
    }

    /**
     * Resolve a page by slug, with fallback to legacy views for known slugs.
     */
    public function resolve(string $slug): View
    {
        $page = Page::where('slug', $slug)->where('published', true)->first();

        if ($page) {
            $seo = \App\Models\SeoPage::where('slug', $slug)->first();
            return view('pages.show', compact('page', 'seo'));
        }

        $fallbackViews = [
            'brand-info' => 'public.brand_info',
            'privacy-and-anonymity' => 'static.privacy_anonymity',
            'security-overview' => 'static.security_overview',
            'sms-opt-in' => 'public.sms_opt_in',
            'sms-opt-in-example' => 'public.sms_opt_in_example',
            'sms-onboarding-sample' => 'public.onboarding_sample',
            'support' => 'static.support',
            'privacy' => 'static.privacy',
            'terms' => 'static.terms',
        ];

        if (array_key_exists($slug, $fallbackViews)) {
            return view($fallbackViews[$slug]);
        }

        abort(404);
    }
}
