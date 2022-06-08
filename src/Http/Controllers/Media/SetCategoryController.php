<?php

namespace Dcodegroup\LaravelAttachments\Http\Controllers\Media;

use Dcodegroup\LaravelAttachments\Http\Requests\Media\SetCategoryRequest;
use Dcodegroup\LaravelAttachments\Models\Media;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class SetCategoryController
{
    use AuthorizesRequests;

    public function __invoke(SetCategoryRequest $request, Media $media): JsonResponse
    {
        $this->authorize('update', $media);

        /**
         * The update is getting performed in  app/Observers/MediaObserver.php
         * saving method. Spo we only need to save here
         */
        $media->save();

        return response()->json([
            'message'     => __('attachments::category.status.update_success'),
            'category_id' => $request->input('category_id'),
        ]);
    }
}
