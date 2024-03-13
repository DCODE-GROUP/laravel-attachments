<?php

namespace Dcodegroup\LaravelAttachments\Http\Controllers\Categories;

use Dcodegroup\LaravelAttachments\Models\MediaCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CategoriesController
{
    use AuthorizesRequests;

    public function __invoke(Request $request): Collection|array
    {
        $query = MediaCategory::query();

        if ($request->filled('type')) {
            $query->byType($request->input('type'));
        }

        $orderBy = 'name';

        if ($request->filled('order')) {
            $orderBy = $request->input('order');
        }

        if ($request->filled('parent_id')) {
            $query->forParentId($request->input('parent_id'));
        } else {
            $query->whereNull('parent_id');
        }

        return $query->orderBy($orderBy)->get();
    }
}
