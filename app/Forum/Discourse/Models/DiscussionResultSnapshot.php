<?php

namespace App\Forum\Discourse\Models;

use Database\Factories\Forum\DiscussionResultSnapshotFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $discussion_id
 * @property string $profile_key
 * @property array<string, int> $weights
 * @property array<int, array<string, mixed>> $position_scores
 * @property array<string, mixed> $breakdown
 * @property Carbon $computed_at
 */
#[UseFactory(DiscussionResultSnapshotFactory::class)]
class DiscussionResultSnapshot extends Model
{
    /** @use HasFactory<DiscussionResultSnapshotFactory> */
    use HasFactory;

    protected $fillable = [
        'discussion_id',
        'profile_key',
        'weights',
        'position_scores',
        'breakdown',
        'computed_at',
    ];

    protected function casts(): array
    {
        return [
            'weights' => 'array',
            'position_scores' => 'array',
            'breakdown' => 'array',
            'computed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Discussion, $this>
     */
    public function discussion(): BelongsTo
    {
        return $this->belongsTo(Discussion::class);
    }
}
