<?php

namespace Database\Factories\Forum;

use App\Forum\Discourse\Models\Argument;
use App\Forum\Discourse\Models\Claim;
use App\Forum\Discourse\Models\Discussion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Argument>
 */
class ArgumentFactory extends Factory
{
    protected $model = Argument::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $claim = Claim::factory()->create();

        return [
            'discussion_id' => $claim->discussion_id,
            'claim_id' => $claim->id,
            'parent_id' => null,
            'author_id' => User::factory(),
            'type' => 'support',
            'body' => fake()->paragraph(),
            'status' => 'open',
            'sort_order' => 0,
        ];
    }

    public function forDiscussion(Discussion $discussion): static
    {
        return $this->state(fn (array $attributes): array => [
            'discussion_id' => $discussion->id,
        ]);
    }

    public function forClaim(Claim $claim): static
    {
        return $this->state(fn (array $attributes): array => [
            'discussion_id' => $claim->discussion_id,
            'claim_id' => $claim->id,
        ]);
    }
}
