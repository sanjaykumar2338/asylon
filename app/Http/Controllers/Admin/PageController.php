<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function create(): View
    {
        $page = new Page();

        return view('admin.pages.create', compact('page'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:pages,slug'],
            'template' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'published' => ['sometimes', 'boolean'],
        ]);

        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['title']);
        }

        $data['published'] = $request->boolean('published', true);

        Page::create($data);

        return redirect()
            ->route('admin.pages.index')
            ->with('ok', __('Page created.'));
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('pages', 'slug')->ignore($page->id)],
            'template' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'published' => ['sometimes', 'boolean'],
        ]);

        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['title']);
        }

        $data['published'] = $request->boolean('published', true);

        $page->update($data);

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('ok', __('Page updated.'));
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('ok', __('Page deleted.'));
    }
}
