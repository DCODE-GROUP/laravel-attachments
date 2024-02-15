<?php

namespace Dcodegroup\LaravelAttachments\Http\Controllers\Media;

use Dcodegroup\LaravelAttachments\Http\Requests\Media\UploadRequest;
use Dcodegroup\LaravelAttachments\Models\Media;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class UploadController
{
    use AuthorizesRequests;

    public function __invoke(UploadRequest $request): JsonResponse
    {
        $this->authorize('create', Media::class);

        if (! $request->hasFile('file')) {
            return response()->json([
                'message' => __('attachments::media.status.upload_failed'),
                'status' => Response::HTTP_EXPECTATION_FAILED,
            ], Response::HTTP_EXPECTATION_FAILED);
        }

        ld($request->all());

        $file = $request->file('file');
        $modelClass = $request->input('modelClass');
        $modelId = $request->input('modelId');
        $model = $modelClass::findOrFail($modelId);
        $type = $file->getMimeType() ? Str::before($file->getMimeType(), '/') : 'default';

        $media = $model->addMediaFromRequest('file')
            ->usingFileName($file->hashName())
            ->withCustomProperties([
                'original_filename' => $file->getClientOriginalName(),
                'encoding_format' => $file->extension(),
            ])
            ->toMediaCollection($type);

        if ($request->filled('category_id')) {
            $media->setCategory($request->input('category_id'));
        }

        return response()->json([
            'message' => __('attachments::media.status.upload_success'),
            'model' => $model,
            'media' => $media,
            'url' => $media->url,
            'thumb_url' => $media->thumb_url,
            'grid_url' => $media->grid_url,
            'status' => Response::HTTP_CREATED,
        ], Response::HTTP_CREATED);
    }
}
