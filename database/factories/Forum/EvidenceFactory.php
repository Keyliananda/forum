<?php

namespace Database\Factories\Forum;

use App\Forum\Discourse\Models\Claim;
use App\Forum\Discourse\Models\Evidence;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Evidence>
 */
class EvidenceFactory extends Factory
{
    protected $model = Evidence::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $claim = Claim::factory()->create();

        return [
            'discussion_id' => $claim->discussion_id,
            'claim_id' => $claim->id,
            'argument_id' => null,
            'author_id' => User::factory(),
            'url' => fake()->url(),
            'doi' => null,
            'title' => rtrim(fake()->sentence(5), '.'),
            'publisher' => fake()->company(),
            'published_at' => null,
            'accessed_at' => now()->toDateString(),
            'excerpt' => fake()->sentence(),
            'locator' => null,
            'stance' => 'supports',
            'verification_status' => 'unverified',
        ];
    }
}
