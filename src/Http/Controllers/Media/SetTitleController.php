<?php

namespace Dcodegroup\LaravelAttachments\Http\Controllers\Media;

use Dcodegroup\LaravelAttachments\Http\Requests\Media\SetTitleRequest;
use Dcodegroup\LaravelAttachments\Models\Media;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SetTitleController
{
    use AuthorizesRequests;

    public function __invoke(SetTitleRequest $request, Media $media): JsonResponse
    {
        $this->authorize('update', $media);

        $media->title = $request->input('title');

        $media->save();

        return response()->json([
            'message'     => __('attachments::category.status.update_success'),
            'title' => $request->input('title'),
        ]);
    }
}
