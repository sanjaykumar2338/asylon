<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        $menus = Menu::withCount('items')->orderBy('name')->get();

        return view('admin.menus.index', compact('menus'));
    }

    public function create(): View
    {
        $menu = new Menu();

        return view('admin.menus.create', compact('menu'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255', Rule::unique('menus', 'location')->whereNotNull('location')],
        ]);

        Menu::create($data);

        return redirect()
            ->route('admin.menus.index')
            ->with('ok', __('Menu created.'));
    }

    public function edit(Menu $menu): View
    {
        $menu->load(['items' => function ($query) {
            $query->orderBy('position');
        }, 'items.page']);

        $pages = \App\Models\Page::orderBy('title')->get();

        return view('admin.menus.edit', compact('menu', 'pages'));
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('menus', 'location')
                    ->whereNotNull('location')
                    ->ignore($menu->id),
            ],
        ]);

        $menu->update($data);

        return redirect()
            ->route('admin.menus.edit', $menu)
            ->with('ok', __('Menu updated.'));
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();

        return redirect()
            ->route('admin.menus.index')
            ->with('ok', __('Menu deleted.'));
    }
}
