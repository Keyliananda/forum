<?php

namespace App\Forum\Discourse\Models;

use App\Models\User;
use Database\Factories\Forum\EvidenceVerificationFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $evidence_id
 * @property int|null $verifier_id
 * @property string $status
 * @property string|null $note
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Evidence $evidence
 * @property-read User|null $verifier
 */
#[UseFactory(EvidenceVerificationFactory::class)]
class EvidenceVerification extends Model
{
    /** @use HasFactory<EvidenceVerificationFactory> */
    use HasFactory;

    protected $fillable = [
        'evidence_id',
        'verifier_id',
        'status',
        'note',
    ];

    /**
     * @return BelongsTo<Evidence, $this>
     */
    public function evidence(): BelongsTo
    {
        return $this->belongsTo(Evidence::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }
}
