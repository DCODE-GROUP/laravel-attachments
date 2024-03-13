<?php

namespace Dcodegroup\LaravelAttachments\Http\Controllers\Categories;

use Dcodegroup\LaravelAttachments\Models\MediaCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class OptionsController
{
    use AuthorizesRequests;

    public function __invoke(Request $request): array
    {
        $query = MediaCategory::query()->with('childrenRecursive');

        if ($request->filled('type')) {
            $query->byType($request->input('type'));
        }

        $orderBy = 'name';

        if ($request->filled('order')) {
            $orderBy = $request->input('order');
        }

        $results = [];

        $this->getCategories($query->whereNull('parent_id')->orderBy($orderBy)->get(), $results);

        return $results;
    }

    private function getCategories(Collection $categories, &$result, $parent_id = 0, int $depth = 0)
    {
        //filter only categories under current "parent"
        $rootCategories = $categories->filter(fn ($item) => $item->parent_id == $parent_id);

        //loop through them
        foreach ($rootCategories as $category) {
            //add category. Don't forget the dashes in front. Use ID as index
            $result[] = [
                'id' => $category->id,
                'name' => str_repeat('-- ', $depth).$category->name,
            ];

            //go deeper - let's look for "children" of current category
            if ($category->childrenRecursive->isNotEmpty()) {
                $this->getCategories($category->childrenRecursive, $result, $category->id, $depth + 1);
            }
        }
    }
}
