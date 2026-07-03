<?php

namespace Database\Factories\Forum;

use App\Forum\Discourse\Models\Argument;
use App\Forum\Reputation\Models\ReputationEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReputationEvent>
 */
class ReputationEventFactory extends Factory
{
    protected $model = ReputationEvent::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $argument = Argument::factory()->create();

        return [
            'recipient_user_id' => User::factory(),
            'actor_user_id' => User::factory(),
            'reputable_type' => Argument::class,
            'reputable_id' => $argument->id,
            'dimension' => 'argument_quality',
            'points' => fake()->numberBetween(1, 10),
            'reason' => fake()->sentence(),
        ];
    }
}
