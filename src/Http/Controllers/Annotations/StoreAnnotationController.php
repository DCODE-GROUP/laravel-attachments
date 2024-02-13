<?php

namespace Dcodegroup\LaravelAttachments\Http\Controllers\Annotations;

use Dcodegroup\LaravelAttachments\Http\Requests\Annotations\StoreAnnotationRequest;
use Dcodegroup\LaravelAttachments\Models\Media;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class StoreAnnotationController
{
    use AuthorizesRequests;

    public function __invoke(StoreAnnotationRequest $request, Media $media): JsonResponse
    {
        $this->authorize('update', $media);

        $media->annotations()->delete();

        $insertableItems = collect($request->input('annotations'))->map(fn (array $item) => array_merge(collect($item)
            ->only([
                'content',
                'hidden',
            ])
            ->toArray(), [
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                    'media_id' => $media->id,
                ]));

        $media->annotations()->insert($insertableItems->toArray());

        return response()->json([
            'message' => __('attachments::annotations.responses.update_success'),
            'annotations' => $media->annotations,
            'status' => 201,
        ], 201);
    }
}
