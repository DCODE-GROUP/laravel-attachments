<?php

namespace Dcodegroup\LaravelAttachments\Http\Controllers\Media;

use Dcodegroup\LaravelAttachments\Http\Requests\Media\ExistingRequest;
use Dcodegroup\LaravelAttachments\Models\Media;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ExistingController
{
    use AuthorizesRequests;

    public function __invoke(ExistingRequest $request)
    {
        $this->authorize('viewAny', Media::class);

        $modelClass = $request->input('modelClass');
        $modelId = $request->input('modelId');
        $model = $modelClass::findOrFail($modelId);

        $mediaQuery = $model->media();

        if ($request->filled('category_id')) {
            $mediaQuery->where('category_id', $request->input('category_id'));
        }

        return $mediaQuery->with(['annotations', 'children', 'children.annotations'])->get()->toArray();
    }
}
