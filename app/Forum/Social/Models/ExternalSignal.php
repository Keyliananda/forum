<?php

namespace App\Forum\Social\Models;

use App\Forum\Discourse\Models\Discussion;
use Database\Factories\Forum\ExternalSignalFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $discussion_id
 * @property string $platform
 * @property string $label
 * @property int $up_count
 * @property int $down_count
 * @property int $comment_count
 * @property string|null $source_url
 * @property Carbon|null $captured_at
 */
#[UseFactory(ExternalSignalFactory::class)]
class ExternalSignal extends Model
{
    /** @use HasFactory<ExternalSignalFactory> */
    use HasFactory;

    protected $fillable = [
        'discussion_id',
        'platform',
        'label',
        'up_count',
        'down_count',
        'comment_count',
        'source_url',
        'captured_at',
    ];

    protected function casts(): array
    {
        return [
            'captured_at' => 'datetime',
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
