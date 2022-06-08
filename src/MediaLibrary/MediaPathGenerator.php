<?php

namespace Dcodegroup\LaravelAttachments\MediaLibrary;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class MediaPathGenerator extends DefaultPathGenerator
{
    /**
     * Get the path for the given media, relative to the root storage path.
     *
     * @param  \Spatie\MediaLibrary\MediaCollections\Models\Media  $media
     *
     * @return string
     */
    public function getPath(Media $media): string
    {
        return Str::afterLast($media->model_type, '\\').'/'.$media->model_id.'/'.$media->id.'/';
    }

    /**
     * Get the path for conversions of the given media, relative to the root storage path.
     *
     * @param  \Spatie\MediaLibrary\MediaCollections\Models\Media  $media
     *
     * @return string
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media).'conversions/';
    }

    /**
     * Get the path for responsive images of the given media, relative to the root storage path.
     *
     * @param \Spatie\MediaLibrary\MediaCollections\Models\Media  $media
     *
     * @return string
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media).'responsive-images/';
    }
}
