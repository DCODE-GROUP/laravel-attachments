<?php

namespace Dcodegroup\LaravelAttachments\Observer;

use Dcodegroup\LaravelAttachments\Models\Media;

class MediaObserver
{
    public function deleted(Media $media)
    {
        $media->deleteChildren();
    }
}
