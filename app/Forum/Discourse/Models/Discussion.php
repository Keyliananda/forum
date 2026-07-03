<?php

namespace App\Forum\Discourse\Models;

use App\Models\User;
use Database\Factories\Forum\DiscussionFactory;
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
 * @property int $topic_id
 * @property int|null $author_id
 * @property string $title
 * @property string $slug
 * @property string $core_question
 * @property string|null $body
 * @property string $status
 * @property string $governance_profile
 * @property Carbon|null $locked_at
 * @property Carbon|null $last_replied_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Space $space
 * @property-read Topic $topic
 * @property-read User|null $author
 */
#[UseFactory(DiscussionFactory::class)]
class Discussion extends Model
{
    /** @use HasFactory<DiscussionFactory> */
    use HasFactory;

    protected $fillable = [
        'space_id',
        'topic_id',
        'author_id',
        'title',
        'slug',
        'core_question',
        'body',
        'status',
        'locked_at',
        'last_replied_at',
    ];

    protected function casts(): array
    {
        return [
            'locked_at' => 'datetime',
            'last_replied_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Discussion $discussion): void {
            $discussion->slug = $discussion->slug ?: (Str::slug($discussion->title) ?: 'discussion');
            $discussion->last_replied_at ??= Carbon::now();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
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
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return HasMany<DiscussionReply, $this>
     */
    public function replies(): HasMany
    {
        return $this->hasMany(DiscussionReply::class)->oldest();
    }

    /**
     * @return HasMany<Position, $this>
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class)->orderBy('sort_order')->oldest();
    }

    /**
     * @return HasMany<Argument, $this>
     */
    public function arguments(): HasMany
    {
        return $this->hasMany(Argument::class)->oldest();
    }

    /**
     * @return HasMany<Evidence, $this>
     */
    public function evidence(): HasMany
    {
        return $this->hasMany(Evidence::class)->oldest();
    }

    public function isOpen(): bool
    {
        return $this->status === 'open' && $this->locked_at === null;
    }
}
