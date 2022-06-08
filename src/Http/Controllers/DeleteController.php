<?php

namespace Dcodegroup\LaravelAttachments\Http\Controllers;

use Dcodegroup\LaravelAttachments\Models\Media;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeleteController
{
    use AuthorizesRequests;

    public function __invoke(Request $request, Media $media): JsonResponse
    {
        $this->authorize('delete', $media);

        $media->model->deleteMedia($media);

        return response()->json([
            'message' => __('attachments::media.status.delete_success'),
        ], Response::HTTP_NO_CONTENT);
    }
}
