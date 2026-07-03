<?php

namespace Database\Factories\Forum;

use App\Forum\Discourse\Models\Evidence;
use App\Forum\Discourse\Models\EvidenceVerification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EvidenceVerification>
 */
class EvidenceVerificationFactory extends Factory
{
    protected $model = EvidenceVerification::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'evidence_id' => Evidence::factory(),
            'verifier_id' => User::factory(),
            'status' => 'verified',
            'note' => fake()->sentence(),
        ];
    }
}
