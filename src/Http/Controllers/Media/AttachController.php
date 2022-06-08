<?php

namespace Dcodegroup\LaravelAttachments\Http\Controllers\Media;

use Dcodegroup\LaravelAttachments\Http\Requests\Media\AttachRequest;
use Dcodegroup\LaravelAttachments\Models\Media;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class AttachController
{
    use AuthorizesRequests;

    public function __invoke(AttachRequest $request): JsonResponse
    {
        $this->authorize('store', Media::class);

        $modelClass = $request->input('modelClass');
        $modelId = $request->input('modelId');
        $model = $modelClass::findOrFail($modelId);
        $media = Media::query()->findOrFail($request->input('media'));

        $type = $media->mime_type ? Str::before($media->mime_type, '/') : 'default';

        $media->copy($model, $type);

        return response()->json(array_merge([
            'message' => __('attachments::media.status.upload_success'),
            'model' => $model,
            'media' => $media,
            'url' => $media->url,
            'thumb_url' => $media->thumb_url,
            'grid_url' => $media->grid_url,
            'status' => Response::HTTP_CREATED,
        ]), Response::HTTP_CREATED);
    }
}
