<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoPage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SeoPageController extends Controller
{
    public function index(): View
    {
        $seoPages = SeoPage::orderBy('slug')->paginate(30);

        return view('admin.seo_pages.index', compact('seoPages'));
    }

    public function edit(SeoPage $seoPage): View
    {
        return view('admin.seo_pages.edit', compact('seoPage'));
    }

    public function update(Request $request, SeoPage $seoPage): RedirectResponse
    {
        $data = $request->validate([
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'h1_override' => ['nullable', 'string', 'max:255'],
        ]);

        $seoPage->update($data);

        return redirect()->route('admin.seo-pages.index')->with('ok', 'SEO settings updated.');
    }
}
