<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuItemController extends Controller
{
    public function store(Request $request, Menu $menu): RedirectResponse
    {
        $data = $this->validateData($request, $menu);

        $data['position'] = $menu->items()->max('position') + 1;

        $menu->items()->create($data);

        return back()->with('ok', __('Menu item created.'));
    }

    public function update(Request $request, Menu $menu, MenuItem $menuItem): RedirectResponse
    {
        abort_unless($menuItem->menu_id === $menu->id, 404);

        $data = $this->validateData($request, $menu, $menuItem);

        $menuItem->update($data);

        return back()->with('ok', __('Menu item updated.'));
    }

    public function destroy(Menu $menu, MenuItem $menuItem): RedirectResponse
    {
        abort_unless($menuItem->menu_id === $menu->id, 404);
        $menuItem->delete();

        return back()->with('ok', __('Menu item deleted.'));
    }

    public function reorder(Request $request, Menu $menu): RedirectResponse
    {
        $orderInput = $request->input('order');
        if (is_string($orderInput)) {
            $orderInput = array_filter(explode(',', $orderInput));
        }

        if (! is_array($orderInput)) {
            return back()->with('error', __('Invalid order payload.'));
        }

        if (count($orderInput) === 1 && is_string($orderInput[0]) && str_contains($orderInput[0], ',')) {
            $orderInput = array_filter(explode(',', $orderInput[0]));
        }

        $order = array_values($orderInput);

        foreach ($order as $index => $id) {
            $menuItem = $menu->items()->whereKey($id)->first();
            if ($menuItem) {
                $menuItem->update(['position' => $index + 1]);
            }
        }

        return back()->with('ok', __('Menu order updated.'));
    }

    protected function validateData(Request $request, Menu $menu, ?MenuItem $menuItem = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['url', 'page'])],
            'url' => ['nullable', 'string', 'max:2048'],
            'page_id' => ['nullable', 'exists:pages,id'],
            'target' => ['nullable', 'string', 'max:50'],
            'parent_id' => ['nullable', 'exists:menu_items,id'],
        ]);

        $data['target'] = $data['target'] ?? '_self';
        $data['parent_id'] = $data['parent_id'] ?? null;

        // Ensure parent belongs to same menu
        if ($data['parent_id']) {
            $parent = MenuItem::find($data['parent_id']);
            if (! $parent || $parent->menu_id !== $menu->id) {
                $data['parent_id'] = null;
            }
        }

        if ($data['type'] === 'page') {
            $data['url'] = null;
        } else {
            $data['page_id'] = null;
        }

        return $data;
    }
}
