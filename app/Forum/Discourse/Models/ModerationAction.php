<?php

namespace App\Forum\Discourse\Models;

use App\Models\User;
use Database\Factories\Forum\ModerationActionFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $moderator_id
 * @property string $actionable_type
 * @property int $actionable_id
 * @property string $action
 * @property string|null $public_reason
 * @property string|null $internal_note
 * @property string $policy_version
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[UseFactory(ModerationActionFactory::class)]
class ModerationAction extends Model
{
    /** @use HasFactory<ModerationActionFactory> */
    use HasFactory;

    protected $fillable = [
        'moderator_id',
        'actionable_type',
        'actionable_id',
        'action',
        'public_reason',
        'internal_note',
        'policy_version',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function actionable(): MorphTo
    {
        return $this->morphTo();
    }
}
