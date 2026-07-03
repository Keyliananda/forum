<?php

namespace Database\Factories\Forum;

use App\Forum\Discourse\Models\Argument;
use App\Forum\Discourse\Models\ArgumentQualityVote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ArgumentQualityVote>
 */
class ArgumentQualityVoteFactory extends Factory
{
    protected $model = ArgumentQualityVote::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'argument_id' => Argument::factory(),
            'user_id' => User::factory(),
            'clarity' => fake()->numberBetween(1, 5),
            'relevance' => fake()->numberBetween(1, 5),
            'logic' => fake()->numberBetween(1, 5),
            'source_usage' => fake()->numberBetween(1, 5),
            'fairness' => fake()->numberBetween(1, 5),
            'rebuttal_strength' => fake()->numberBetween(1, 5),
            'note' => fake()->optional()->sentence(),
        ];
    }
}
