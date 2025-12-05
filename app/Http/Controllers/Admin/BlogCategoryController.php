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
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:blog_categories,slug'],
        ]);

        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['name']);
        }

        BlogCategory::create($data);

        return back()->with('ok', 'Category created.');
    }

    public function update(Request $request, BlogCategory $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('blog_categories', 'slug')->ignore($category->id)],
        ]);

        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category->update($data);

        return back()->with('ok', 'Category updated.');
    }

    public function destroy(BlogCategory $category): RedirectResponse
    {
        $category->delete();

        return back()->with('ok', 'Category deleted.');
    }
}
