<?php

namespace App\Forum\Discourse\Models;

use App\Models\User;
use Database\Factories\Forum\DiscussionReplyFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $discussion_id
 * @property int|null $parent_id
 * @property int|null $author_id
 * @property string $body
 * @property string $status
 * @property Carbon|null $hidden_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Discussion $discussion
 * @property-read User|null $author
 */
#[UseFactory(DiscussionReplyFactory::class)]
class DiscussionReply extends Model
{
    /** @use HasFactory<DiscussionReplyFactory> */
    use HasFactory;

    protected $fillable = [
        'discussion_id',
        'parent_id',
        'author_id',
        'body',
        'status',
        'hidden_at',
    ];

    protected function casts(): array
    {
        return [
            'hidden_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Discussion, $this>
     */
    public function discussion(): BelongsTo
    {
        return $this->belongsTo(Discussion::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return BelongsTo<DiscussionReply, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(DiscussionReply::class, 'parent_id');
    }

    /**
     * @return HasMany<DiscussionReply, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(DiscussionReply::class, 'parent_id')->oldest();
    }
}
