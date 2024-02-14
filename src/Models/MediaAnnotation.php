<?php

namespace Dcodegroup\LaravelAttachments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaAnnotation extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]|bool
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'hidden' => 'bool',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}
