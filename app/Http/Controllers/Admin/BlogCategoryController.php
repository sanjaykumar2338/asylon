<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $formLocale = $this->resolveLocale($request);
        $categories = BlogCategory::orderBy('name')->get();

        return view('admin.blog.categories.index', compact('categories', 'formLocale'));
    }

    public function store(Request $request): RedirectResponse
    {
        $languages = array_keys(config('asylon.languages', []));
        $defaultLocale = config('app.fallback_locale', 'en');
        $data = $request->validate([
            'locale' => ['required', 'string', Rule::in($languages)],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:blog_categories,slug'],
        ]);

        $locale = $data['locale'];
        unset($data['locale']);

        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['name']);
        } else {
            $data['slug'] = Str::slug($data['slug']);
        }

        $category = BlogCategory::create($data);

        $category->setTranslation('name', $locale, $data['name']);

        if ($locale === $defaultLocale) {
            foreach ($languages as $language) {
                if (! filled($category->getTranslationValue('name', $language))) {
                    $category->setTranslation('name', $language, $data['name']);
                }
            }
        } else {
            if (! filled($category->getTranslationValue('name', $defaultLocale))) {
                $category->setTranslation('name', $defaultLocale, $data['name']);
            }
        }

        $category->save();

        return back()->with('ok', 'Category created.');
    }

    public function update(Request $request, BlogCategory $category): RedirectResponse
    {
        $languages = array_keys(config('asylon.languages', []));
        $defaultLocale = config('app.fallback_locale', 'en');
        $locale = $request->input('locale', $defaultLocale);

        $rawSlug = $request->input('slug');
        $rawName = $request->input('name');
        $slug = filled($rawSlug) ? Str::slug($rawSlug) : null;

        if (blank($slug) && $locale === $defaultLocale && filled($rawName)) {
            $slug = Str::slug($rawName);
        }

        $slug = $slug ?: $category->slug;

        $slugRule = [
            'nullable',
            'string',
            'max:255',
            'alpha_dash',
        ];

        // Only enforce unique when slug changes to avoid false positives on unchanged slugs
        if ($slug !== $category->slug) {
            $slugRule[] = Rule::unique('blog_categories', 'slug')->ignore($category->id);
        }

        $data = $request->validate([
            'locale' => ['required', 'string', Rule::in($languages)],
            'name' => [$locale === $defaultLocale ? 'required' : 'nullable', 'string', 'max:255'],
            'slug' => $slugRule,
        ]);

        $locale = $data['locale'];
        unset($data['locale']);

        $data['slug'] = $slug;

        if ($locale === $defaultLocale) {
            $category->fill($data);
        } else {
            $category->fill(['slug' => $data['slug']]);
        }

        $category->save();

        if (array_key_exists('name', $data)) {
            $category->setTranslation('name', $locale, $data['name']);
        }

        if ($locale === $defaultLocale) {
            $category->setTranslation('name', $defaultLocale, $data['name']);

            foreach ($languages as $language) {
                if (! filled($category->getTranslationValue('name', $language))) {
                    $category->setTranslation('name', $language, $data['name']);
                }
            }
        }

        $category->save();

        return back()->with('ok', 'Category updated.');
    }

    public function destroy(BlogCategory $category): RedirectResponse
    {
        $category->delete();

        return back()->with('ok', 'Category deleted.');
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
