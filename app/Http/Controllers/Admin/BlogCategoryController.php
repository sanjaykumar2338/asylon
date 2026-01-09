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
    public function index(): View
    {
        $categories = BlogCategory::orderBy('name')->get();

        return view('admin.blog.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'slug' => $request->filled('slug')
                ? Str::slug($request->input('slug'))
                : Str::slug($request->input('name')),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:blog_categories,slug'],
        ]);

        BlogCategory::create($data);

        return back()->with('ok', 'Category created.');
    }

    public function update(Request $request, BlogCategory $category): RedirectResponse
    {
        $request->merge([
            'slug' => $request->filled('slug')
                ? Str::slug($request->input('slug'))
                : Str::slug($request->input('name')),
        ]);

        $slug = $request->input('slug');

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
            'name' => ['required', 'string', 'max:255'],
            'slug' => $slugRule,
        ]);

        $category->update($data);

        return back()->with('ok', 'Category updated.');
    }

    public function destroy(BlogCategory $category): RedirectResponse
    {
        $category->delete();

        return back()->with('ok', 'Category deleted.');
    }
}
