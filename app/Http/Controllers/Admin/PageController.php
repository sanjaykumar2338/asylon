<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index(): View
    {
        $pages = Page::orderByDesc('updated_at')->paginate(20);

        return view('admin.pages.index', compact('pages'));
    }

    public function create(Request $request): View
    {
        $page = new Page();
        $formLocale = $this->resolveLocale($request);

        return view('admin.pages.create', compact('page', 'formLocale'));
    }

    public function store(Request $request): RedirectResponse
    {
        $languages = array_keys(config('asylon.languages', []));
        $defaultLocale = config('app.fallback_locale', 'en');

        $data = $request->validate([
            'locale' => ['required', 'string', Rule::in($languages)],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:pages,slug'],
            'template' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'published' => ['sometimes', 'boolean'],
        ]);

        $locale = $data['locale'];
        unset($data['locale']);

        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['title']);
        }

        $data['published'] = $request->boolean('published', true);

        $page = Page::create($data);

        $translatable = ['title', 'excerpt', 'meta_title', 'meta_description', 'meta_keywords', 'content'];
        $translationPayload = Arr::only($data, $translatable);

        foreach ($translatable as $field) {
            if (array_key_exists($field, $translationPayload)) {
                $page->setTranslation($field, $locale, $translationPayload[$field]);
            }
        }

        if ($locale === $defaultLocale) {
            foreach ($languages as $language) {
                foreach ($translatable as $field) {
                    if (! filled($page->getTranslationValue($field, $language))) {
                        $page->setTranslation($field, $language, $translationPayload[$field] ?? null);
                    }
                }
            }
        } else {
            foreach ($translatable as $field) {
                if (! filled($page->getTranslationValue($field, $defaultLocale))) {
                    $page->setTranslation($field, $defaultLocale, $translationPayload[$field] ?? null);
                }
            }
        }

        $page->save();

        return redirect()
            ->route('admin.pages.edit', ['page' => $page, 'lang' => $locale])
            ->with('ok', __('Page created.'));
    }

    public function edit(Request $request, Page $page): View
    {
        $formLocale = $this->resolveLocale($request);

        return view('admin.pages.edit', compact('page', 'formLocale'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $languages = array_keys(config('asylon.languages', []));
        $defaultLocale = config('app.fallback_locale', 'en');
        $locale = $request->input('locale', $defaultLocale);

        $data = $request->validate([
            'locale' => ['required', 'string', Rule::in($languages)],
            'title' => [$locale === $defaultLocale ? 'required' : 'nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('pages', 'slug')->ignore($page->id)],
            'template' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'published' => ['sometimes', 'boolean'],
        ]);

        $locale = $data['locale'];
        unset($data['locale']);

        if (blank($data['slug'] ?? null)) {
            if ($locale === $defaultLocale) {
                $data['slug'] = Str::slug($data['title'] ?? $page->getRawOriginal('title'));
            } else {
                unset($data['slug']);
            }
        }

        $data['published'] = $request->boolean('published', $page->published);

        $translatable = ['title', 'excerpt', 'meta_title', 'meta_description', 'meta_keywords', 'content'];
        $translationPayload = Arr::only($data, $translatable);
        $baseData = Arr::except($data, $translatable);

        if ($locale === $defaultLocale) {
            $page->fill($baseData + $translationPayload);
        } else {
            $page->fill($baseData);
        }

        $page->save();

        foreach ($translatable as $field) {
            if (array_key_exists($field, $translationPayload)) {
                $page->setTranslation($field, $locale, $translationPayload[$field]);
            }
        }

        if ($locale === $defaultLocale) {
            foreach ($translatable as $field) {
                if (array_key_exists($field, $translationPayload)) {
                    $page->setTranslation($field, $defaultLocale, $translationPayload[$field]);
                }
            }

            foreach ($languages as $language) {
                foreach ($translatable as $field) {
                    if (! filled($page->getTranslationValue($field, $language))) {
                        $page->setTranslation($field, $language, $translationPayload[$field] ?? null);
                    }
                }
            }
        }

        $page->save();

        return redirect()
            ->route('admin.pages.edit', ['page' => $page, 'lang' => $locale])
            ->with('ok', __('Page updated.'));
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('ok', __('Page deleted.'));
    }

    protected function resolveLocale(Request $request): string
    {
        $languages = array_keys(config('asylon.languages', []));
        $defaultLocale = config('app.fallback_locale', 'en');
        $locale = $request->query('lang', $defaultLocale);

        if (! in_array($locale, $languages, true)) {
            $locale = $defaultLocale;
        }

        app()->setLocale($locale);

        return $locale;
    }
}
