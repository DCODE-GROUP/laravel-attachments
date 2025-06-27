<?php

namespace Dcodegroup\LaravelAttachments\Http\Controllers\Media;

use Dcodegroup\LaravelAttachments\Models\Media;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController
{
    use AuthorizesRequests;

    public function __invoke(Request $request, Media $media): BinaryFileResponse
    {
        $this->authorize('download', $media);

        return response()->download(
            file: Storage::disk($media->disk)
                ->path($media->getPath()),
            name: $media->custom_properties->original_filename
        );
    }
}
