<?php

namespace App\Forum\Discourse\Models;

use Database\Factories\Forum\TopicFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $space_id
 * @property int|null $parent_id
 * @property string $name
 * @property string $slug
 * @property string $path
 * @property int $depth
 * @property string|null $description
 * @property int|null $created_by_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Space $space
 * @property-read Topic|null $parent
 */
#[UseFactory(TopicFactory::class)]
class Topic extends Model
{
    /** @use HasFactory<TopicFactory> */
    use HasFactory;

    protected $fillable = [
        'space_id',
        'parent_id',
        'name',
        'slug',
        'path',
        'depth',
        'description',
        'created_by_user_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (Topic $topic): void {
            $topic->slug = $topic->slug ?: Str::slug($topic->name);

            if ($topic->parent_id !== null) {
                $parent = Topic::query()->findOrFail($topic->parent_id);
                $topic->space_id = $parent->space_id;
                $topic->depth = $parent->depth + 1;
                $topic->path = trim($parent->path.'/'.$topic->slug, '/');

                return;
            }

            $topic->depth = 0;
            $topic->path = $topic->slug;
        });
    }

    public function getRouteKeyName(): string
    {
        return 'path';
    }

    /**
     * @return BelongsTo<Space, $this>
     */
    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    /**
     * @return BelongsTo<Topic, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Topic::class, 'parent_id');
    }

    /**
     * @return HasMany<Topic, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Topic::class, 'parent_id')->orderBy('name');
    }

    /**
     * @return HasMany<Discussion, $this>
     */
    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class)->latest('last_replied_at')->latest();
    }
}
