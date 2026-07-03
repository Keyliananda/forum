<?php

namespace App\Forum\Discourse\Models;

use App\Models\User;
use Database\Factories\Forum\PositionFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $discussion_id
 * @property int|null $author_id
 * @property string $title
 * @property string $slug
 * @property string|null $summary
 * @property string $status
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Discussion $discussion
 * @property-read User|null $author
 */
#[UseFactory(PositionFactory::class)]
class Position extends Model
{
    /** @use HasFactory<PositionFactory> */
    use HasFactory;

    protected $table = 'discussion_positions';

    protected $fillable = [
        'discussion_id',
        'author_id',
        'title',
        'slug',
        'summary',
        'status',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::creating(function (Position $position): void {
            $position->slug = $position->slug ?: (Str::slug($position->title) ?: 'position');
        });
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
     * @return BelongsToMany<Claim, $this, PositionClaim, 'assignment'>
     */
    public function claims(): BelongsToMany
    {
        return $this->belongsToMany(Claim::class, 'discussion_position_claims', 'position_id', 'claim_id')
            ->using(PositionClaim::class)
            ->as('assignment')
            ->withPivot(['author_id', 'sort_order'])
            ->withTimestamps()
            ->orderByPivot('sort_order')
            ->orderBy('discussion_claims.created_at');
    }
}
