<?php

namespace App\Forum\Discourse\Models;

use App\Models\User;
use Database\Factories\Forum\ResultProfileFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string|null $key
 * @property string $name
 * @property array<string, int> $weights
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[UseFactory(ResultProfileFactory::class)]
class ResultProfile extends Model
{
    /** @use HasFactory<ResultProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'key',
        'name',
        'weights',
    ];

    protected function casts(): array
    {
        return [
            'weights' => 'array',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
