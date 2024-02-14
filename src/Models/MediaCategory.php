<?php

namespace Dcodegroup\LaravelAttachments\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaCategory extends Model
{
    use SoftDeletes;

    final public const TYPE_ATTACHMENT = 'attachment';

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('name');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    /**
     * @return mixed
     */
    public static function attachmentCategories()
    {
        return self::query()->byType(self::TYPE_ATTACHMENT)->get();
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeForParentId(Builder $query, int $parentId): Builder
    {
        return $query->where('parent_id', $parentId);
    }
}
