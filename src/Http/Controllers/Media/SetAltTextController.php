<?php

namespace Dcodegroup\LaravelAttachments\Http\Controllers\Media;

use Dcodegroup\LaravelAttachments\Http\Requests\Media\SetAltTextRequest;
use Dcodegroup\LaravelAttachments\Models\Media;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SetAltTextController
{
    use AuthorizesRequests;

    public function __invoke(SetAltTextRequest $request, Media $media): JsonResponse
    {
        $this->authorize('update', $media);

        $media->alt_text = $request->input('alt_text');

        $media->save();

        return response()->json([
            'message'     => __('attachments::category.status.update_success'),
            'alt_text' => $request->input('alt_text'),
        ]);
    }
}
