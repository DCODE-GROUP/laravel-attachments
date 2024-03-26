<?php

namespace Dcodegroup\LaravelAttachments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaAnnotation extends Model
{
    protected $fillable = [
        'content',
        'media_id',
        'hidden',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hidden' => 'bool',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}
