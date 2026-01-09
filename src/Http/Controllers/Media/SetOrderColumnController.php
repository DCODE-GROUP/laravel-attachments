<?php

namespace Dcodegroup\LaravelAttachments\Http\Controllers\Media;

use Dcodegroup\LaravelAttachments\Http\Requests\Media\SetOrderColumnRequest;
use Dcodegroup\LaravelAttachments\Models\Media;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class SetOrderColumnController
{
    use AuthorizesRequests;

    public function __invoke(SetOrderColumnRequest $request, Media $media): JsonResponse
    {
        $this->authorize('update', $media);

        $media->order_column = $request->input('order_column');

        $media->save();

        return response()->json([
            'message' => __('attachments::media.status.update_success'),
            'order_column' => $request->input('order_column'),
        ]);
    }
}
