<?php

namespace App\Forum\Discourse\Models;

use App\Models\User;
use Database\Factories\Forum\ArgumentQualityVoteFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $argument_id
 * @property int $user_id
 * @property int $clarity
 * @property int $relevance
 * @property int $logic
 * @property int $source_usage
 * @property int $fairness
 * @property int $rebuttal_strength
 * @property string|null $note
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[UseFactory(ArgumentQualityVoteFactory::class)]
class ArgumentQualityVote extends Model
{
    /** @use HasFactory<ArgumentQualityVoteFactory> */
    use HasFactory;

    protected $fillable = [
        'argument_id',
        'user_id',
        'clarity',
        'relevance',
        'logic',
        'source_usage',
        'fairness',
        'rebuttal_strength',
        'note',
    ];

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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function averageScore(): float
    {
        return round((
            $this->clarity
            + $this->relevance
            + $this->logic
            + $this->source_usage
            + $this->fairness
            + $this->rebuttal_strength
        ) / 6, 1);
    }
}
