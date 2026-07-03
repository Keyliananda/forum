<?php

namespace App\Forum\Discourse\Models;

use App\Models\User;
use Database\Factories\Forum\ArgumentFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $discussion_id
 * @property int|null $claim_id
 * @property int|null $parent_id
 * @property int|null $author_id
 * @property string $type
 * @property string $body
 * @property string $status
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Discussion $discussion
 * @property-read Claim|null $claim
 * @property-read Argument|null $parent
 * @property-read User|null $author
 */
#[UseFactory(ArgumentFactory::class)]
class Argument extends Model
{
    /** @use HasFactory<ArgumentFactory> */
    use HasFactory;

    protected $table = 'discussion_arguments';

    protected $fillable = [
        'discussion_id',
        'claim_id',
        'parent_id',
        'author_id',
        'type',
        'body',
        'status',
        'sort_order',
    ];

    /**
     * @return BelongsTo<Discussion, $this>
     */
    public function discussion(): BelongsTo
    {
        return $this->belongsTo(Discussion::class);
    }

    /**
     * @return BelongsTo<Claim, $this>
     */
    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    /**
     * @return BelongsTo<Argument, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Argument::class, 'parent_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return HasMany<Argument, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Argument::class, 'parent_id')->oldest();
    }

    /**
     * @return HasMany<Evidence, $this>
     */
    public function evidence(): HasMany
    {
        return $this->hasMany(Evidence::class)->oldest();
    }
}
