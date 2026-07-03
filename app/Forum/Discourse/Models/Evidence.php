<?php

namespace App\Forum\Discourse\Models;

use App\Models\User;
use Database\Factories\Forum\EvidenceFactory;
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
 * @property int|null $argument_id
 * @property int|null $author_id
 * @property string|null $url
 * @property string|null $doi
 * @property string $title
 * @property string|null $publisher
 * @property Carbon|null $published_at
 * @property Carbon|null $accessed_at
 * @property string|null $excerpt
 * @property string|null $locator
 * @property string $stance
 * @property string $verification_status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Discussion $discussion
 * @property-read Claim|null $claim
 * @property-read Argument|null $argument
 */
#[UseFactory(EvidenceFactory::class)]
class Evidence extends Model
{
    /** @use HasFactory<EvidenceFactory> */
    use HasFactory;

    protected $table = 'discussion_evidence';

    protected static function booted(): void
    {
        static::creating(function (Evidence $evidence): void {
            if ($evidence->claim_id !== null) {
                $evidence->discussion_id = Claim::query()->findOrFail($evidence->claim_id)->discussion_id;
            }

            if ($evidence->argument_id !== null) {
                $evidence->discussion_id = Argument::query()->findOrFail($evidence->argument_id)->discussion_id;
            }
        });
    }

    protected $fillable = [
        'discussion_id',
        'claim_id',
        'argument_id',
        'author_id',
        'url',
        'doi',
        'title',
        'publisher',
        'published_at',
        'accessed_at',
        'excerpt',
        'locator',
        'stance',
        'verification_status',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'date',
            'accessed_at' => 'date',
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
     * @return BelongsTo<Claim, $this>
     */
    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    /**
     * @return BelongsTo<Argument, $this>
     */
    public function argument(): BelongsTo
    {
        return $this->belongsTo(Argument::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return HasMany<EvidenceVerification, $this>
     */
    public function verifications(): HasMany
    {
        return $this->hasMany(EvidenceVerification::class)->latest();
    }
}
