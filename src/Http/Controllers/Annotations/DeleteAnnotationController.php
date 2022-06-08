<?php

namespace Dcodegroup\LaravelAttachments\Http\Controllers\Annotations;

use Dcodegroup\LaravelAttachments\Models\Annotation;
use Dcodegroup\LaravelAttachments\Models\Media;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class DeleteAnnotationController
{
    use AuthorizesRequests;

    public function __invoke(Media $media, Annotation $annotation): JsonResponse
    {
        $this->authorize('delete', $media);

        $annotation->delete();

        return response()->json([
            'message' => __('attachments::annotations.responses.delete_success'),
            'status'  => 201,
        ], 201);
    }
}
