<?php

namespace Dcodegroup\LaravelAttachments\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Imagick;
use ImagickException;

class Media extends \Spatie\MediaLibrary\MediaCollections\Models\Media
{
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'category',
    ];

    /**
     * The attributes that should be appended to model arrays.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'add_annotation_endpoint',
        'grid_url',
        'list_url',
        'original_url',
        'preview_url',
        'thumb_url',
        'url',
        'delete_endpoint',
    ];

    private $applicationImage = null;

    public function getDeleteEndpointAttribute(): string
    {
        return route(config('attachments.route_name_prefix').'.media.destroy', $this);
    }


    public function getThumbUrlAttribute(): string
    {
        return $this->getUrl('thumb');
    }

    public function getListUrlAttribute(): string
    {
        return $this->getUrl('list');
    }

    public function getGridUrlAttribute(): string
    {
        return $this->getUrl('grid');
    }

    public function isPDF(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function getUrlAttribute(): string
    {
        return $this->getUrl();
    }

    public function parentModel(): MorphTo
    {
        return $this->morphTo();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MediaCategory::class);
    }

    public function annotations(): HasMany
    {
        return $this->hasMany(MediaAnnotation::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    private function getImageUrl(?string $conversionName = '')
    {
        if ($this->parent_id) {
            return Storage::disk(config('filesystems.default'))->url($this->file_name);
        }

        if ($this->preview_application_url) {
            return $this->preview_application_url;
        }

        return parent::getUrl($conversionName);
    }

    public function getAddAnnotationEndpointAttribute()
    {
        return route(config('attachments.route_name_prefix').'.media.annotations.store', $this);
    }

    public function getPreviewApplicationUrlAttribute()
    {
        if ($this->applicationImage || $this->applicationImage === false) {
            return $this->applicationImage;
        }

        $child = $this->children()->first();

        if ($child) {
            $this->applicationImage = Storage::disk(config('filesystems.default'))->url($child->file_name);

            return $this->applicationImage;
        }

        $this->applicationImage = false;

        return $this->applicationImage;
    }

    public function deleteChildren()
    {
        $this->children->each(function (self $media) {
            Storage::disk(config('filesystems.default'))->delete($media->file_name);
            $media->delete();
        });
    }

    public function generatePages()
    {
        $images = collect([]);
        try {
            $images = collect(new Imagick($this->getUrl()));
        } catch (ImagickException $e) {
            report($e);
        }

        $images->each(function (Imagick $image, $key) {
            $image->setIteratorIndex($key);
            $image->setImageFormat('jpg');
            $fileName = 'pages/'.$this->hashName();
            Storage::disk(config('filesystems.default'))->put($fileName, $image->getImageBlob());
            self::create([
                'name' => "$this->name-page-$key.jpg",
                'file_name' => $fileName,
                'parent_id' => $this->id,
                'order_column' => $key,
                'disk' => config('filesystems.default'),
                'manipulations' => [],
                'custom_properties' => [],
                'generated_conversions' => [],
                'conversions_disk' => config('filesystems.default'),
                'responsive_images' => [],
                'collection_name' => 'image',
                'mime_type' => 'image/jpeg',
                'size' => $image->getImageLength(),
            ]);
        });
    }

    /**
     * Get a filename for the file.
     *
     *
     * @return string
     */
    public function hashName(string $extension = '.jpg')
    {
        $hash = Str::random(40);

        return $hash.$extension;
    }

    public function setCategory(int $categoryId): void
    {
        $this->update([
            'category_id' => $categoryId,
        ]);
    }
}
