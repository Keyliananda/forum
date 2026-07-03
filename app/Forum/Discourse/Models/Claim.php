<?php

namespace App\Forum\Discourse\Models;

use App\Models\User;
use Database\Factories\Forum\ClaimFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $discussion_id
 * @property int|null $author_id
 * @property string $statement
 * @property string $slug
 * @property string $type
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $author
 */
#[UseFactory(ClaimFactory::class)]
class Claim extends Model
{
    /** @use HasFactory<ClaimFactory> */
    use HasFactory;

    protected $table = 'discussion_claims';

    protected $fillable = [
        'discussion_id',
        'author_id',
        'statement',
        'slug',
        'type',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (Claim $claim): void {
            $claim->slug = $claim->slug ?: (Str::slug($claim->statement) ?: 'claim');
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
     * @return BelongsToMany<Position, $this, PositionClaim, 'assignment'>
     */
    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'discussion_position_claims', 'claim_id', 'position_id')
            ->using(PositionClaim::class)
            ->as('assignment')
            ->withPivot(['author_id', 'sort_order'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<Argument, $this>
     */
    public function arguments(): HasMany
    {
        return $this->hasMany(Argument::class)->whereNull('parent_id')->oldest();
    }

    /**
     * @return HasMany<Evidence, $this>
     */
    public function evidence(): HasMany
    {
        return $this->hasMany(Evidence::class)->oldest();
    }
}
