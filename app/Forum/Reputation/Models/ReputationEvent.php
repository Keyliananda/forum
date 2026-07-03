<?php

namespace App\Forum\Reputation\Models;

use App\Models\User;
use Database\Factories\Forum\ReputationEventFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $recipient_user_id
 * @property int|null $actor_user_id
 * @property string $reputable_type
 * @property int $reputable_id
 * @property string $dimension
 * @property int $points
 * @property string $reason
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[UseFactory(ReputationEventFactory::class)]
class ReputationEvent extends Model
{
    /** @use HasFactory<ReputationEventFactory> */
    use HasFactory;

    protected $fillable = [
        'recipient_user_id',
        'actor_user_id',
        'reputable_type',
        'reputable_id',
        'dimension',
        'points',
        'reason',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function reputable(): MorphTo
    {
        return $this->morphTo();
    }
}
