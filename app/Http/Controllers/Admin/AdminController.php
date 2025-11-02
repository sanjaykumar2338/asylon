<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Org;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

abstract class AdminController extends Controller
{
    /**
     * Scope the provided query based on the authenticated user's role.
     *
     * @template T of \Illuminate\Database\Eloquent\Model
     *
     * @param  Builder<T>  $query
     * @param  string  $column
     * @return Builder<T>
     */
    protected function scopeByRole(Builder $query, string $column = 'org_id'): Builder
    {
        $user = auth()->user();

        if ($user && ! $user->hasRole('platform_admin')) {
            $query->where($column, $user->org_id);
        }

        return $query;
    }

    /**
     * Get the list of organizations visible to the authenticated admin.
     *
     * @return Collection<int, Org>
     */
    protected function orgOptions(): Collection
    {
        $user = auth()->user();

        $query = Org::query()->orderBy('name');

        if ($user && ! $user->hasRole('platform_admin')) {
            $query->whereKey($user->org_id);
        }

        return $query->get();
    }
}
