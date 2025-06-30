<?php

namespace Dcodegroup\LaravelAttachments\Http\Controllers\Media;

use Dcodegroup\LaravelAttachments\Models\Media;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController
{
    use AuthorizesRequests;

    public function __invoke(Request $request, Media $media): StreamedResponse
    {
        $this->authorize('download', $media);

        return Storage::disk($media->disk)
            ->download(
                path: $media->getPath(),
                name: $media->getCustomProperty('original_filename')
            );
    }
}
