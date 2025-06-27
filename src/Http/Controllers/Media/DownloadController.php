<?php

namespace Dcodegroup\LaravelAttachments\Http\Controllers\Media;

use Dcodegroup\LaravelAttachments\Models\Media;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController
{
    use AuthorizesRequests;

    public function __invoke(Request $request, Media $media): BinaryFileResponse
    {
        $this->authorize('download', $media);

        return response()->download(
            file: $media->original_url,
            name: $media->custom_properties->original_filename
        );
    }
}
