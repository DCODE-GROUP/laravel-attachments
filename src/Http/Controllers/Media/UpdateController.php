<?php

namespace Dcodegroup\LaravelAttachments\Http\Controllers\Media;

use Dcodegroup\LaravelAttachments\Http\Requests\Media\UpdateRequest;
use Dcodegroup\LaravelAttachments\Models\Media;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class UpdateController
{
    use AuthorizesRequests;

    public function __invoke(UpdateRequest $request, Media $media)
    {
        $this->authorize('create', Media::class);
        $file = $request->file('file');
        $modelClass = $media->model_type;
        $modelId = $media->model_id;
        $model = $modelClass::findOrFail($modelId);

        $type = $file->getMimeType() ? Str::before($file->getMimeType(), '/') : 'default';

        $newMedia = $model->addMediaFromRequest('file')
            ->usingFileName($file->hashName())
            ->withCustomProperties([
                'original_filename' => $file->getClientOriginalName(),
                'encoding_format' => $file->extension(),
            ])
            ->toMediaCollection($type);
        $newMedia->order_column = $media->order_column;
        $newMedia->category_id = $media->category_id;
        $newMedia->save();
        $media->delete();

        return response()->json([
            'message' => __('attachments::media.status.update_success'),
            'model' => $newMedia,
            'media' => $newMedia,
            'url' => $newMedia->url,
            'thumb_url' => $newMedia->thumb_url,
            'grid_url' => $newMedia->grid_url,
            'status' => Response::HTTP_CREATED,
        ], Response::HTTP_CREATED);
    }
}
